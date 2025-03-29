<?php

namespace Modules\ExchangeRates\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExchangeRatesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('exchangerates::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('exchangerates::create');
    }

    /**
     * Stores a newly created exchange rate resource.
     *
     * Processes the incoming HTTP request to create a new exchange rate.
     * The implementation for persisting the resource is currently not provided.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the view for a specific exchange rate resource.
     *
     * This method returns the view responsible for displaying the details of an exchange rate based on the provided identifier.
     *
     * @param mixed $id The identifier of the exchange rate resource.
     *
     * @return \Illuminate\View\View The view for displaying the exchange rate.
     */
    public function show($id)
    {
        return view('exchangerates::show');
    }

    /**
     * Display the edit form for the specified exchange rate.
     *
     * Returns the view used for editing an exchange rate resource identified by its ID.
     *
     * @param mixed $id The identifier of the exchange rate resource to edit.
     * @return \Illuminate\Contracts\View\View The view instance for editing the exchange rate.
     */
    public function edit($id)
    {
        return view('exchangerates::edit');
    }

    /**
     * Update the specified exchange rate resource.
     *
     * Uses the provided request data to update the exchange rate resource identified by its unique ID.
     *
     * @param mixed $id The unique identifier of the exchange rate resource.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified exchange rate resource from storage.
     *
     * This method deletes the exchange rate resource identified by the given identifier.
     *
     * @param mixed $id The unique identifier of the exchange rate resource to remove.
     */
    public function destroy($id)
    {
        //
    }
}
