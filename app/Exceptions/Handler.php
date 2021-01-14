<?php

namespace App\Exceptions;

use Arr;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use URL;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * @var array
     */
    protected $dontBack = [
        ValidationException::class,
        AuthenticationException::class
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $e)
    {
        return $request->expectsJson() || $this->isDontBackException($e)
            ? parent::render($request, $e)
            : $this->redirectBackOrHome($request, $e);
    }

    /**
     * @param Throwable $e
     * @return bool
     */
    public function isDontBackException(Throwable $e): bool
    {
        foreach ($this->dontBack as $exception) {
            if ($e instanceof $exception) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $request
     * @param Throwable $e
     * @return JsonResponse|RedirectResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    protected function redirectBackOrHome($request, Throwable $e)
    {
        $current = URL::current();
        $previous = URL::previous();
        if ($e instanceof ModelNotFoundException) {
            flash()->error('flash.exception.ModelNotFoundException');
        } else if ($e instanceof AuthorizationException) {
            flash()->error('flash.exception.AuthorizationException');
        } else if ($e instanceof TokenMismatchException) {
            flash()->error('flash.exception.TokenMismatchException');
        } else if ($e instanceof NotFoundHttpException) {
            return parent::prepareResponse($request, $e);
        } else {
            flash()->error('flash.exception.OtherException');
        }
        return ($current !== $previous)
            ? redirect()->back()->withInput()
            : $this->redirectHome($request, $e);
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse|RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $guard = Arr::get($exception->guards(), 0);
        switch ($guard) {
            case 'admin':
                return redirect()->route('admin.auth.login');
            default:
                return $this->prepareResponse($request, $exception);
        }
    }

    /**
     * @param $request
     * @param Throwable $e
     * @return JsonResponse|RedirectResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    protected function redirectHome($request, Throwable $e)
    {
        $url = null;
        if (auth()->guard('admin')->check()) {
            $url = route('admin.home.index');
        }

        $current = URL::current();
        if (null !== $url && $current !== $url) {
            return redirect()->to($url);
        }

        return parent::render($request, $e);
    }
}
