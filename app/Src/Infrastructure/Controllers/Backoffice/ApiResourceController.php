<?php

namespace App\Src\Infrastructure\Controllers\Backoffice;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Inertia\Inertia;

abstract class ApiResourceController extends BaseController
{
    /**
     * Respond appropriately based on the request type.
     *
     * @param Request $request
     * @param mixed $data
     * @param string|null $view
     * @param array $extraProps
     * @return mixed
     */
    protected function respond(Request $request, $data, ?string $view = null, array $extraProps = [])
    {
        // Detect if the request is from Inertia
        if ($request->inertia()) {

            // If no view is provided, use a generic wrapper component
            $view = $view ?? 'WrapperPage'; // Generic wrapper component

            // Render the Inertia response
            return Inertia::render($view, array_merge([
                'title' => $extraProps['title'] ?? 'Dynamic Response',
                'data' => $data,
            ], $extraProps));
        }

        // For non-Inertia requests, return JSON
        return response()->json($data);
    }
}
