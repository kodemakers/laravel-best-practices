<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponser
{

	private function successResponse($data, $code)
	{
		return response()->json([
			'data' => $data,
			'status' => '1',

		], $code);
	}

	protected function errorResponse($message, $code)
	{
		return response()->json([
			'message' => $message,
			'status' => '0',

		], $code);
	}

	protected function paginate(Collection $collection)
	{

		$rules = [
			'per_page' => 'integer|min:2|max:50',
		];

		Validator::validate(request()->all(), $rules);

		$page = LengthAwarePaginator::resolveCurrentPage();

		$perPage = 20;

		if (request()->has('per_page')) {
			$perPage = (int)request()->per_page;
		}

		$result = $collection->slice(($page - 1) * $perPage, $perPage)->values();

		$paginated = new LengthAwarePaginator($result, $collection->count(), $perPage, $page, ['path' => LengthAwarePaginator::resolveCurrentPath(),]);

		$paginated->appends(request()->all());

		return $paginated;
	}
}
