<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'IBM Plex Sans Arabic', Arial, sans-serif;
            direction: rtl;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 5px;
        }

        h2,
        h5,
        p {
            margin: 0;
            padding: 0;
        }

        .header {
            color: white;
            padding: 10px;
        }

        .contact-box {
            border: 1px solid black;
            padding: 15px;
            margin-top: 20px;
            width: 100%;
        }

        .divider {
            height: 1px;
            background-color: black;
            width: 35%;
            margin: 20px auto;
        }

        .watermark {
            position: relative;
            width: 100%;
            text-align: center;
            margin-top: 50px;
        }

        .watermark img {
            width: 300px;
            opacity: 0.2;
        }
    </style>
    <title>bill</title>
</head>

<body>
    <div style="background-color: #2e004c; color: white; padding: 10px; border-bottom-left-radius: 50px; border-bottom-right-radius: 50px;overflow:hidden">
        <table class="header">
            <tr>
                <td style="width: 70%; text-align: right;">
                    <h2 style="font-weight: 500; margin-right: 30px; font-size: 1.75rem;">إيصال استلام نقدية</h2>
                </td>
                <td style="width: 30%; text-align: left;">
                    <img src="{{ base_path('public/asset/invoice/images/aura.png') }}" class="aura.png" alt="شعار الأكاديمية"
                        style="width: 220px; height: auto; margin-top: -85px; margin-bottom: -85px;">
                </td>
            </tr>
        </table>
    </div>

    <table
        style="width: 100%; border-collapse: collapse; text-align: right; font-size: 1.2rem; font-weight: bold; line-height: 2;">
        <tr>
            <td style="width: 33%; padding: 5px;">إنه في يوم : {{ $date }} </td>
            <td style="width: 33%; padding: 5px;">الموافق : {{ $day }} </td>
            <td style="width: 33%; padding: 5px;">الفرع : {{ $branch }} </td>
        </tr>
        <tr>
            <td style="padding: 5px;" colspan="2">إستلمنا من السيد : {{ $name }} </td>
            <td style="padding: 5px;">رقم التليفون : {{ $phone }} </td>
        </tr>
        <tr>
            <td style="padding: 5px;">مبلغ و قدرة : {{ $amount }} </td>
            <td style="padding: 5px;"></td>
            <td style="padding: 5px;">المبلغ المتبقي : {{ $remaining }} </td>
        </tr>
        <tr>
            <td style="padding: 5px;" colspan="3">وذلك قيمة : {{ $value }} </td>
        </tr>
        <tr>
            <td style="padding: 5px;" colspan="3">والتي تشمل : {{ $include }} </td>
        </tr>
        <tr>
            <td style="padding: 5px;">الدفع من خلال : {{ $method }} </td>
            <td style="padding: 5px;"> </td>
            <td style="padding: 5px;">طريقه الدفع: {{ $type }} </td>

        </tr>
        <tr>
            <td style="padding: 5px;" colspan="3">مسؤول الحجز : {{ $admin_name }} </td>
        </tr>
    </table>


    <!-- الصندوق السفلي -->
    <div style="display: flex; justify-content: center;">
        <div style="border: 1px solid black; padding: 15px; max-width: 800px; width: 100%; margin-top: 20px;">
            <div>
                <h5 style="font-weight: bold;">للتواصل مع الإدارة</h5>
                <h5 style="font-weight: bold;">يمكنك التواصل مع الإدارة الخاصة بالأكاديمية بشكل مباشر من خلال الأرقام
                    التالية</h5>
                <p style="font-size: 1rem;">
                    <span style="font-weight: bold;">&bull; ٠١١٤٧٠٩٩٠٩٩</span><br>
                    <span style="font-weight: bold;">&bull; ٠١١٤٠٧٦٦٠١٣٧</span>
                </p>
            </div>
        </div>
    </div>

    <div style="height: 1px; background-color: black; width: 35%; margin: 20px auto;"></div>

    <div style="font-family: 'IBM Plex Sans Arabic'; text-align: center; margin-top: 20px; position: relative;">
        <p style="font-size: 1.1rem;font-weight: bold;">سياسة الشركة وشروط حجز دورة تدريبية</p>
        <p style="font-size: 1.1rem;font-weight: bold;">
            على العميل قراءة ورقة الشروط جيدًا - قراءة سياسة الشركة هي مسؤولية العميل
        </p>
        <img src="{{ base_path('public/asset/invoice/images/academy.png') }}"
            style="width: 300px; height: auto; opacity: 0.3; margin: 20px auto; display: block;margin-top: -40px;"
            alt="شعار أورا">
    </div>

</body>


</html>
