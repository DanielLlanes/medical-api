<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Landing\BaseLandingController;
use App\Models\Landlord\Faq;
use App\Models\Landlord\Plan;
use Illuminate\Http\JsonResponse;

class PlanController extends BaseLandingController
{

    public function index(): JsonResponse
    {

        $plans = Plan::active()->select(['name', 'slug', 'price', 'features', 'limit_users', 'code'])->get();
        $faqs = Faq::active()->byCategory('venta')->ordered()->select('question', 'answer')->get();

        return $this->sendResponse(
            [
                'plans' => $plans,
                'faqs'  => $faqs
            ],
            'Tarifario actualizado recuperado.',
            200
        );
    }
}
