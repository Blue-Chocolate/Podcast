<?php

namespace App\Http\Controllers\Api\SubscriberController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        // ✅ التحقق من المدخلات
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|unique:subscribers,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'فشل التسجيل',
                'errors' => $validator->errors()
            ], 422);
        }

        // ✅ حفظ البيانات
        $subscriber = Subscriber::create([
            'first_name' => $request->first_name,
            'email' => $request->email,
        ]);

        // ✅ إرسال ايميل ترحيبي
        try {
            Mail::raw("مرحبًا {$subscriber->first_name}! شكرًا لاشتراكك في نشرتنا الإخبارية ❤️", function ($message) use ($subscriber) {
                $message->to($subscriber->email)
                        ->subject('أهلًا بك في النشرة الإخبارية!');
            });
        } catch (\Exception $e) {
            // لو حصل خطأ في الإرسال
            return response()->json([
                'status' => true,
                'message' => 'تم التسجيل لكن فشل إرسال البريد الإلكتروني.',
                'error' => $e->getMessage()
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم التسجيل بنجاح ✅ و تم إرسال رسالة الترحيب.',
        ], 200);
    }
}
