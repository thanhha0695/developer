<?php

namespace App\Http\Controllers\Admin\Example;

use App\Http\Controllers\Controller;
use App\Http\Requests\Example\ExampleRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Class ExampleController
 * @package App\Http\Controllers\Admin\Example
 */
class ExampleController extends Controller
{

    /**
     * example
     *
     * @return Application|Factory|View
     */
    public function example()
    {
        return view('auth.login');
    }

    /**
     * example validate
     *
     * @param ExampleRequest $request
     * @return RedirectResponse
     */
    public function exampleValidate(ExampleRequest $request)
    {
        $input = $request->all();
        return redirect()->route('admin.example');
    }
}
