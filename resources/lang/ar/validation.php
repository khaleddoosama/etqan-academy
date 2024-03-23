<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ترجمة رسائل الصحة
    |--------------------------------------------------------------------------
    |
    | السطور التالية تحتوي على رسائل الخطأ الافتراضية المستخدمة من قبل
    | فئة المدقق (الفاليداتور). بعض هذه القواعد لديها إصدارات متعددة مثل
    | قواعد الحجم. تفضل بتعديل كل من هذه الرسائل حسب الحاجة هنا.
    |
    */

    'accepted' => 'يجب قبول حقل :attribute.',
    'accepted_if' => 'يجب قبول حقل :attribute عندما :other يكون :value.',
    'active_url' => 'حقل :attribute يجب أن يحتوي على رابط صالح.',
    'after' => 'حقل :attribute يجب أن يكون تاريخًا بعد :date.',
    'after_or_equal' => 'حقل :attribute يجب أن يكون تاريخًا بعد أو مساويًا لـ :date.',
    'alpha' => 'حقل :attribute يجب أن يحتوي على أحرف فقط.',
    'alpha_dash' => 'حقل :attribute يجب أن يحتوي على أحرف وأرقام وشرطات وشرطات سفلية فقط.',
    'alpha_num' => 'حقل :attribute يجب أن يحتوي على أحرف وأرقام فقط.',
    'array' => 'حقل :attribute يجب أن يكون مصفوفة.',
    'ascii' => 'حقل :attribute يجب أن يحتوي على أحرف أبجدية أحادية البايت ورموزًا.',
    'before' => 'حقل :attribute يجب أن يكون تاريخًا قبل :date.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخًا قبل أو مساويًا لـ :date.',
    'between' => [
        'array' => 'حقل :attribute يجب أن يحتوي على بين :min و :max عناصر.',
        'file' => 'حقل :attribute يجب أن يكون بين :min و :max كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون بين :min و :max.',
        'string' => 'حقل :attribute يجب أن يكون بين :min و :max حروفًا.',
    ],
    'boolean' => 'حقل :attribute يجب أن يكون صحيحًا أو خاطئًا.',
    'can' => 'حقل :attribute يحتوي على قيمة غير مصرح بها.',
    'confirmed' => 'تأكيد حقل :attribute لا يتطابق.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'حقل :attribute يجب أن يكون تاريخًا صالحًا.',
    'date_equals' => 'حقل :attribute يجب أن يكون تاريخًا مساويًا لـ :date.',
    'date_format' => 'حقل :attribute يجب أن يتطابق مع الشكل :format.',
    'decimal' => 'حقل :attribute يجب أن يحتوي على :decimal أماكن عشرية.',
    'declined' => 'حقل :attribute يجب رفضه.',
    'declined_if' => 'حقل :attribute يجب رفضه عندما :other يكون :value.',
    'different' => 'حقل :attribute و :other يجب أن يكونا مختلفين.',
    'digits' => 'حقل :attribute يجب أن يحتوي على :digits أرقام.',
    'digits_between' => 'حقل :attribute يجب أن يكون بين :min و :max أرقام.',
    'dimensions' => 'حقل :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'حقل :attribute يحتوي على قيمة مكررة.',
    'doesnt_end_with' => 'حقل :attribute يجب ألا ينتهي بأحد القيم التالية: :values.',
    'doesnt_start_with' => 'حقل :attribute يجب ألا يبدأ بأحد القيم التالية: :values.',
    'email' => 'حقل :attribute يجب أن يكون عنوان بريد إلكتروني صالح.',
    'ends_with' => 'حقل :attribute يجب أن ينتهي بأحد القيم التالية: :values.',
    'enum' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'exists' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'file' => 'حقل :attribute يجب أن يكون ملفًا.',
    'filled' => 'حقل :attribute يجب أن يحتوي على قيمة.',
    'gt' => [
        'array' => 'حقل :attribute يجب أن يحتوي على أكثر من :value عنصر.',
        'file' => 'حقل :attribute يجب أن يكون أكبر من :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أكبر من :value.',
        'string' => 'حقل :attribute يجب أن يحتوي على أكثر من :value حرف.',
    ],
    'gte' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :value عنصر أو أكثر.',
        'file' => 'حقل :attribute يجب أن يكون أكبر من أو مساويًا لـ :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أكبر من أو مساويًا لـ :value.',
        'string' => 'حقل :attribute يجب أن يكون أكبر من أو مساويًا لـ :value حرف.',
    ],
    'image' => 'حقل :attribute يجب أن يكون صورة.',
    'in' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'in_array' => 'حقل :attribute يجب أن يكون موجودًا في :other.',
    'integer' => 'حقل :attribute يجب أن يكون عددًا صحيحًا.',
    'ip' => 'حقل :attribute يجب أن يكون عنوان IP صالحًا.',
    'ipv4' => 'حقل :attribute يجب أن يكون عنوان IPv4 صالحًا.',
    'ipv6' => 'حقل :attribute يجب أن يكون عنوان IPv6 صالحًا.',
    'json' => 'حقل :attribute يجب أن يكون نص JSON صالحًا.',
    'lowercase' => 'حقل :attribute يجب أن يكون بأحرف صغيرة.',
    'lt' => [
        'array' => 'حقل :attribute يجب أن يحتوي على أقل من :value عنصر.',
        'file' => 'حقل :attribute يجب أن يكون أقل من :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أقل من :value.',
        'string' => 'حقل :attribute يجب أن يحتوي على أقل من :value حرف.',
    ],
    'lte' => [
        'array' => 'حقل :attribute يجب ألا يحتوي على أكثر من :value عنصر.',
        'file' => 'حقل :attribute يجب أن يكون أقل من أو مساويًا لـ :value كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون أقل من أو مساويًا لـ :value.',
        'string' => 'حقل :attribute يجب أن يكون أقل من أو مساويًا لـ :value حرف.',
    ],
    'mac_address' => 'حقل :attribute يجب أن يكون عنوان MAC صالحًا.',
    'max' => [
        'array' => 'حقل :attribute يجب ألا يحتوي على أكثر من :max عنصر.',
        'file' => 'حقل :attribute يجب ألا يكون أكبر من :max كيلوبايت.',
        'numeric' => 'حقل :attribute يجب ألا يكون أكبر من :max.',
        'string' => 'حقل :attribute يجب ألا يكون أكبر من :max حرف.',
    ],
    'max_digits' => 'حقل :attribute يجب ألا يحتوي على أكثر من :max أرقام.',
    'mimes' => 'حقل :attribute يجب أن يكون ملفًا من نوع: :values.',
    'mimetypes' => 'حقل :attribute يجب أن يكون ملفًا من نوع: :values.',
    'min' => [
        'array' => 'حقل :attribute يجب أن يحتوي على على الأقل :min عنصر.',
        'file' => 'حقل :attribute يجب أن يكون على الأقل :min كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون على الأقل :min.',
        'string' => 'حقل :attribute يجب أن يكون على الأقل :min حرف.',
    ],
    'min_digits' => 'حقل :attribute يجب أن يحتوي على على الأقل :min أرقام.',
    'missing' => 'حقل :attribute يجب أن يكون مفقودًا.',
    'missing_if' => 'حقل :attribute يجب أن يكون مفقودًا عندما :other يكون :value.',
    'missing_unless' => 'حقل :attribute يجب أن يكون مفقودًا ما لم :other يكون :value.',
    'missing_with' => 'حقل :attribute يجب أن يكون مفقودًا عندما تكون القيم التالية موجودة: :values.',
    'missing_with_all' => 'حقل :attribute يجب أن يكون مفقودًا عندما تكون القيم التالية موجودة: :values.',
    'multiple_of' => 'حقل :attribute يجب أن يكون مضاعفًا لـ :value.',
    'not_in' => 'القيمة المحددة لـ :attribute غير صالحة.',
    'not_regex' => 'تنسيق حقل :attribute غير صالح.',
    'numeric' => 'حقل :attribute يجب أن يكون رقمًا.',
    'password' => [
        'letters' => 'حقل :attribute يجب أن يحتوي على على الأقل حرف واحد.',
        'mixed' => 'حقل :attribute يجب أن يحتوي على على الأقل حرف كبير وحرف صغير ورقم ورمز واحد على الأقل.',
        'numbers' => 'حقل :attribute يجب أن يحتوي على على الأقل رقم واحد.',
        'symbols' => 'حقل :attribute يجب أن يحتوي على على الأقل رمز واحد.',
        'uncompromised' => 'القيمة المعطاة لـ :attribute قد ظهرت في تسرب بيانات. الرجاء اختيار قيمة مختلفة لـ :attribute.',
    ],
    'present' => 'حقل :attribute يجب أن يكون موجودًا.',
    'prohibited' => 'حقل :attribute محظور.',
    'prohibited_if' => 'حقل :attribute محظور عندما :other يكون :value.',
    'prohibited_unless' => 'حقل :attribute محظور ما لم :other يكون في :values.',
    'prohibits' => 'حقل :attribute يمنع :other من التواجد.',
    'regex' => 'تنسيق حقل :attribute غير صالح.',
    'required' => 'حقل :attribute مطلوب.',
    'required_array_keys' => 'حقل :attribute يجب أن يحتوي على مدخلات للقيم التالية: :values.',
    'required_if' => 'حقل :attribute مطلوب عندما :other يكون :value.',
    'required_if_accepted' => 'حقل :attribute مطلوب عندما يتم قبول :other.',
    'required_unless' => 'حقل :attribute مطلوب ما لم :other يكون في :values.',
    'required_with' => 'حقل :attribute مطلوب عندما تكون القيم التالية موجودة: :values.',
    'required_with_all' => 'حقل :attribute مطلوب عندما تكون القيم التالية موجودة: :values.',
    'required_without' => 'حقل :attribute مطلوب عندما تكون القيم التالية غير موجودة: :values.',
    'required_without_all' => 'حقل :attribute مطلوب عندما لا تكون القيم التالية موجودة: :values.',
    'same' => 'حقل :attribute يجب أن يتطابق مع :other.',
    'size' => [
        'array' => 'حقل :attribute يجب أن يحتوي على :size عنصر.',
        'file' => 'حقل :attribute يجب أن يكون :size كيلوبايت.',
        'numeric' => 'حقل :attribute يجب أن يكون :size.',
        'string' => 'حقل :attribute يجب أن يكون :size حرف.',
    ],
    'starts_with' => 'حقل :attribute يجب أن يبدأ بأحد القيم التالية: :values.',
    'string' => 'حقل :attribute يجب أن يكون نصًا.',
    'timezone' => 'حقل :attribute يجب أن يكون منطقة زمنية صالحة.',
    'unique' => 'تم اختيار :attribute بالفعل.',
    'uploaded' => 'فشل في تحميل :attribute.',
    'uppercase' => 'حقل :attribute يجب أن يكون بأحرف كبيرة.',
    'url' => 'حقل :attribute يجب أن يكون رابطًا صالحًا.',
    'ulid' => 'حقل :attribute يجب أن يكون ULID صالحًا.',
    'uuid' => 'حقل :attribute يجب أن يكون UUID صالحًا.',

    /*
    |--------------------------------------------------------------------------
    | ترجمة رسائل الصحة المخصصة
    |--------------------------------------------------------------------------
    |
    | هنا يمكنك تحديد رسائل تحقق مخصصة للسمات باستخدام
    | التسمية "attribute.rule" لتسمية الخطوط. يساعد هذا على
    | تحديد رسالة لغة مخصصة معينة لقاعدة محددة للسمة.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ترجمة سمات الصحة المخصصة
    |--------------------------------------------------------------------------
    |
    | السطور التالية تستخدم لاستبدال سمة العلامة التوضيحية الخاصة بنا
    | مع شيء أكثر وضوحًا للقارئ مثل "عنوان البريد الإلكتروني" بدلاً
    | من "email". هذا يساعدنا ببساطة على جعل رسائلنا أكثر تعبيرًا.
    |
    */

    'attributes' => [],

];
