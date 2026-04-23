<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\RedirectResponse;
use Throwable;
use Toastr;

trait HandlesAdminActionRedirects
{
    protected function handleActionRedirect(
        callable $action,
        string $successMessage,
        string $errorPrefix,
        ?string $redirectRoute = null,
        bool $withInputOnError = false
    ): RedirectResponse {
        try {
            $action();

            return $redirectRoute
                ? $this->redirectWithSuccessToRoute($redirectRoute, $successMessage)
                : $this->redirectBackWithSuccess($successMessage);
        } catch (Throwable $exception) {
            return $this->redirectBackWithError($errorPrefix . $exception->getMessage(), $withInputOnError);
        }
    }

    protected function handleActionResult(
        callable $action,
        callable $successResponse,
        string $errorPrefix,
        bool $withInputOnError = false
    ): RedirectResponse {
        try {
            return $successResponse($action());
        } catch (Throwable $exception) {
            return $this->redirectBackWithError($errorPrefix . $exception->getMessage(), $withInputOnError);
        }
    }

    protected function redirectWithSuccessToRoute(string $route, string $message): RedirectResponse
    {
        Toastr::success('Success', $message);

        return redirect()->route($route);
    }

    protected function redirectBackWithSuccess(string $message): RedirectResponse
    {
        Toastr::success('Success', $message);

        return redirect()->back();
    }

    protected function redirectBackWithError(string $message, bool $withInput = false): RedirectResponse
    {
        Toastr::error('Error', $message);

        $redirect = redirect()->back();

        return $withInput ? $redirect->withInput() : $redirect;
    }
}
