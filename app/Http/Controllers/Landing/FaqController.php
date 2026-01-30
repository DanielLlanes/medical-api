<?php

namespace App\Http\Controllers\Landing;;

use App\Models\Landlord\Faq;
use Illuminate\Http\JsonResponse;

class FaqController extends BaseLandingController
{

	public function index(): JsonResponse
	{

		$faqs = Faq::active()->get();

		return $this->sendResponse(
			[
				'faqs'  => $faqs
			],
			'Tarifario actualizado recuperado.',
			200
		);
	}
}
