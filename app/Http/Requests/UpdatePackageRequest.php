<?php

namespace App\Http\Requests;

// Pacote não tem campo único; as regras de atualização são as mesmas da criação.
class UpdatePackageRequest extends StorePackageRequest {}
