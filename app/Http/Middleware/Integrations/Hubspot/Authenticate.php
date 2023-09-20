<?php

namespace App\Http\Middleware\Integrations\Hubspot;

use App\Exceptions\Integrations\Hubspot\UserNotAuthenticatedException;
use App\Services\Integrations\Hubspot\AuthService;
use App\Services\Log\SystemLoggingService;
use Closure;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as RequestFacade;

class Authenticate
{
    public function __construct(protected AuthService $authService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse|JsonResponse
     * @throws Exception
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse|JsonResponse
    {
        $block_id = $request->input('block_id');
        try {
            $this->authService->getToken(Auth::user()->id, $block_id);
        } catch (UserNotAuthenticatedException $exception) {
            $oauth_url = $this->authService->getOAuthUrl(Auth::user()->id, $block_id);
            return redirect()->route('user.integrations.blocks.hubspot.auth', compact('oauth_url'));
        } catch (GuzzleException $exception) {
            app(SystemLoggingService::class)->logErrorToChannel('hubspot_integration', $exception->getMessage(), [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'request' => [
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'data' => $request->all()
                ]
            ]);
            return $next($request);
        }
        return $next($request);
    }
}
