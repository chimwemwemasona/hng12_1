<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class NumberClassificationController extends Controller
{
    public function classify_number(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'number' => $request->number,
                'error' => true
            ], 400);
        }

        $number = $request->number;

        $funFact = Http::get("http://numbersapi.com/{$number}/math")
            ->body();

            $properties = [];

            if ($number % 2 === 0) {
                $properties[] = "even";
            } else {
                $properties[] = "odd";
            }

            if ($this->is_armstrong($number)) {
                array_unshift($properties, "armstrong");
            }

            $digitSum = array_sum(str_split(abs($number)));

            return response()->json([
                'number' => $number,
                'is_prime' => $this->is_prime($number),
                'is_perfect' => $this->is_perfect($number),
                'properties' => $properties,
                'digit_sum' => $digitSum,
                'fun_fact' => $funFact
            ], 200);
    }

    private function is_armstrong(int $number): bool
    {
        $digits = str_split(abs($number));
        $power = strlen((string)abs($number));
        $sum = array_reduce($digits, function($carry, $digit) use ($power) {
            return $carry + pow($digit, $power);
        }, 0);
        
        return $sum === abs($number);
    }

    private function is_prime(int $number): bool
    {
        if ($number < 2) {
            return false;
        }

        for ($i = 2; $i <= sqrt($number); $i++) {
            if ($number % $i === 0) {
                return false;
            }
        }

        return true;
    }

    private function is_perfect(int $number): bool
    {
        if ($number < 1) {
            return false;
        }

        $sum = 0;
        for ($i = 1; $i <= $number / 2; $i++) {
            if ($number % $i === 0) {
                $sum += $i;
            }
        }

        return $sum === $number;
    }
}
