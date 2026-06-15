<?php

use App\Http\Controllers\Api\TripController;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request) => new UserResource($request->user()))
    ->middleware('auth:sanctum');

// RNF03: lista todas as viagens em JSON. Sistema administrativo, sem consumo
// externo previsto → a API exige autenticação (princípio do menor privilégio).
Route::get('/trips', [TripController::class, 'index'])
    ->middleware('auth:sanctum');
