<?php

namespace App\Http\Controllers\User\CustomBlocks;

use App\Exceptions\Integrations\Hubspot\HubspotApiException;
use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Integrations\Hubspot\ContactDealRequest;
use App\Http\Requests\Integrations\Hubspot\DealSettingsRequest;
use App\Http\Requests\Request;
use App\Http\Resources\Integrations\Hubspot\HubspotErrorResource;
use App\Http\Resources\Integrations\Hubspot\HubspotSuccessResource;
use App\Services\Integrations\Hubspot\AuthService;
use App\Services\Integrations\Hubspot\HtmlParams\ContactDealsParams;
use App\Services\Integrations\Hubspot\HtmlParams\DealSettingsParams;
use App\Services\Integrations\Hubspot\HtmlParams\IndexParams;
use App\Services\Integrations\Hubspot\HubspotContactService;
use App\Services\Log\SystemLoggingService;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;
use UseDesk\Hubspot\Book\Book;


class HubspotBlockController extends Controller
{
    /**
     * Кнопка авторизации в Hubspot
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function auth(Request $request): View|Factory|Application
    {
        $oauth_url = $request->get('oauth_url');
        return view('user.custom_blocks.hubspot.oauth_button', compact('oauth_url'));
    }

    /**
     * Сюда перенаправляется пользователь (с кодом) после авторизации в Hubspot
     *
     * @param AuthService $authService
     * @param Request $request
     * @return Factory|View|Application
     */
    public function redirect(AuthService $authService, Request $request): Factory|View|Application
    {
        try {
            $code = $request->input(Book::CODE);
            $state = json_decode($request->input(Book::STATE), true);
            $user_id = $state[AuthService::USER_ID];
            $block_id = $state[AuthService::BLOCK_ID];
            $authService->saveCode($user_id, $block_id, $code);
            return  view('user.custom_blocks.hubspot.auth_redirect', ['success' => true]);
        } catch (Exception $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'request' => [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'data' => $request->all()
                ]
            ]);
            return  view('user.custom_blocks.hubspot.auth_redirect', ['success' => false]);
        }
    }

    /**
     * Главная (после авторизации)
     *
     * @param IndexParams $paramsService
     * @return JsonResource
     */
    public function index(IndexParams $paramsService): JsonResource
    {
        try {
            $params = $paramsService->getForIndexByRequest(
                Auth::user()->id,
                RequestFacade::input('block_id'),
                RequestFacade::all()
            );

            $html = $this->getRenderView('user.custom_blocks.hubspot.index', $params);
            return new HubspotSuccessResource(compact('html'));
        } catch (HubspotApiException|UserNotAuthenticatedException|Exception|GuzzleException $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errors = [trans('Error') => trans('Can not get contacts')];
            return new HubspotErrorResource(compact('errors'));
        }
    }

    /**
     * Форма создания контакта в Hubspot
     *
     * @param HubspotContactService $contactService
     * @return JsonResource
     */
    public function createContact(HubspotContactService $contactService): JsonResource
    {
        try {
            $contact = $contactService->getContactByTicketId(RequestFacade::input('ticket_id'));
            $html = $this->getRenderView('user.custom_blocks.hubspot.create_contact', compact('contact'));
            return new HubspotSuccessResource(compact('html'));
        } catch (Exception $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errors = [trans('Error') => trans('Can not create contact')];
            return new HubspotErrorResource(compact('errors'));
        }

    }

    /**
     * Сохранение контакта в Hubspot
     *
     * @param IndexParams $paramsService
     * @param Request $request
     * @return JsonResource
     */
    public function storeContact(IndexParams $paramsService, Request $request): JsonResource
    {
        try {
            $params = $paramsService->getForIndexWithNewContact(
                Auth::user()->id,
                RequestFacade::input('block_id'),
                $request->all()
            );
            $html = $this->getRenderView('user.custom_blocks.hubspot.index', $params);
            return new HubspotSuccessResource(compact('html'));
        } catch (HubspotApiException $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errorMessage = trans('Can not create contact with this email');
        } catch (UserNotAuthenticatedException $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errorMessage = trans('Connection error');
        }

        $errors = [trans('Error') => $errorMessage];
        return new HubspotErrorResource(compact('errors'));

    }

    /**
     * Список сделок, связанных с контактом
     *
     * @param ContactDealsParams $paramsService
     * @return JsonResource
     */
    public function contactDeals(ContactDealsParams $paramsService): JsonResource
    {
        try {
            $params = $paramsService->getForContactDeals(
                Auth::user()->id,
                RequestFacade::input('block_id'),
                RequestFacade::input('ticket_id'),
                RequestFacade::input('hs_contact_id')
            );

            $html = $this->getRenderView('user.custom_blocks.hubspot.deals', $params);
            return new HubspotSuccessResource(compact('html'));
        } catch (UserNotAuthenticatedException|HubspotApiException|GuzzleException $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errors = [trans('Error') => trans('Can not get deals')];
            return new HubspotErrorResource(compact('errors'));
        }
    }

    /**
     * Форма создания сделки, связанной с контактом
     *
     * @param ContactDealsParams $paramsService
     * @return JsonResource
     * @throws GuzzleException
     */
    public function contactDealCreate(ContactDealsParams $paramsService): JsonResource
    {
        try {
            $params = $paramsService->getForContactDealCreate(
                Auth::user()->id,
                RequestFacade::input('block_id'),
                RequestFacade::input('hs_contact_id')
            );

            $html = $this->getRenderView('user.custom_blocks.hubspot.create_deal', $params);
            return new HubspotSuccessResource(compact('html'));
        } catch (HubspotApiException|UserNotAuthenticatedException $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errors = [trans('Error') => trans('Can not create deal')];
            return new HubspotErrorResource(compact('errors'));
        }
    }

    /**
     * Сохранение сделки, связанной с контактом
     *
     * @param ContactDealsParams $paramsService
     * @param ContactDealRequest $request
     * @return JsonResource
     */
    public function contactDealStore(ContactDealsParams $paramsService, ContactDealRequest $request): JsonResource
    {
        try {
            $params = $paramsService->getForContactDealStore(
                Auth::user()->id,
                $request->input('block_id'),
                $request->input('ticket_id'),
                $request->input('hs_contact_id'),
                $request->all()
            );
            $html = $this->getRenderView('user.custom_blocks.hubspot.show_deal', $params);
            return new HubspotSuccessResource(compact('html'));
        } catch (GuzzleException|HubspotApiException|UserNotAuthenticatedException $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errors = [trans('Error') => trans('Can not create deal')];
            return new HubspotErrorResource(compact('errors'));
        }
    }

    /**
     * Детали сделки, связанной с контактом
     *
     * @param ContactDealsParams $paramsService
     * @return JsonResource
     */
    public function contactDealShow(ContactDealsParams $paramsService): JsonResource
    {
        try {
            $params = $paramsService->getForContactDealShow(
                Auth::user()->id,
                RequestFacade::input('block_id'),
                RequestFacade::input('hs_deal_id'),
                RequestFacade::input('hs_contact_id')
            );

            $html = $this->getRenderView(
                    'user.custom_blocks.hubspot.show_deal', $params
            );
            return new HubspotSuccessResource(compact('html'));

        } catch (GuzzleException|HubspotApiException|UserNotAuthenticatedException|Exception $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errors = [trans('Error') => trans('Can not show deal')];
            return new HubspotErrorResource(compact('errors'));
        }
    }

    /**
     * Страница настроек отображения полей сделки
     *
     * @param DealSettingsParams $paramsService
     * @param Request $request
     * @return HubspotSuccessResource|HubspotErrorResource
     */
    public function editDealSettings(
        DealSettingsParams $paramsService,
        Request $request
    ): HubspotSuccessResource|HubspotErrorResource
    {
        try {
            $params = $paramsService->getForEdit(
                Auth::user()->id,
                $request->input('block_id'),
                $request->input('hs_contact_id'),
            );
            $html = $this->getRenderView(
                'user.custom_blocks.hubspot.edit_deal_settings', $params
            );
            return new HubspotSuccessResource(compact('html'));

        } catch (Exception $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errors = [trans('Error') => trans('Can not show deal settings')];
            return new HubspotErrorResource(compact('errors'));
        }
    }

    /**
     * Сохранение новых настроек отображения полей сделки
     *
     * @param DealSettingsParams $paramsService
     * @param DealSettingsRequest $request
     * @return HubspotSuccessResource|HubspotErrorResource
     */
    public function updateDealSettings(DealSettingsParams $paramsService, DealSettingsRequest $request): HubspotSuccessResource|HubspotErrorResource
    {
        try {
            $params = $paramsService->getForUpdate(
                Auth::user()->id,
                $request->input('block_id'),
                $request->input('ticket_id'),
                $request->input('hs_contact_id'),
                $request->input('deal_settings'),
            );

            $html = $this->getRenderView(
                'user.custom_blocks.hubspot.deals',
                $params
            );
            return new HubspotSuccessResource(compact('html'));
        } catch (GuzzleException|HubspotApiException|UserNotAuthenticatedException $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception);
            $errors = [trans('Error') => trans('Can not update deal settings')];
            return new HubspotErrorResource(compact('errors'));
        }
    }
    /**
     * Отрисовка html с обязательными параметрами
     *
     * @param string $name
     * @param $params
     * @return string
     */
    protected function getRenderView(string $name, $params = []): string
    {
        $params['ticket_id'] = RequestFacade::input('ticket_id');
        return view($name, $params)->render();
    }
}
