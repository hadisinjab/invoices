<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كود إعادة تعيين كلمة المرور</title>
    <style>
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #7b2ff2 0%, #f357a8 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .content {
            padding: 40px 30px;
        }
        .code-container {
            background-color: #f8f9fa;
            border: 2px dashed #7b2ff2;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin: 30px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #7b2ff2;
            letter-spacing: 5px;
            font-family: 'Courier New', monospace;
        }
        .info {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning {
            background-color: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #7b2ff2 0%, #f357a8 100%);
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 25px;
            margin: 10px 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>نظام إدارة الفواتير</h1>
            <p>كود إعادة تعيين كلمة المرور</p>
        </div>

        <div class="content">
            <h2>مرحباً {{ $user->name }}!</h2>

            <p>لقد تلقينا طلباً لإعادة تعيين كلمة المرور الخاصة بحسابك في نظام إدارة الفواتير.</p>

            <div class="code-container">
                <p><strong>كود التحقق الخاص بك هو:</strong></p>
                <div class="code">{{ $code }}</div>
                <p><small>أدخل هذا الكود في صفحة إعادة تعيين كلمة المرور</small></p>
            </div>

            <div class="info">
                <strong>معلومات مهمة:</strong>
                <ul>
                    <li>هذا الكود صالح لمدة 15 دقيقة فقط</li>
                    <li>لا تشارك هذا الكود مع أي شخص</li>
                    <li>إذا لم تطلب إعادة تعيين كلمة المرور، يمكنك تجاهل هذا البريد الإلكتروني</li>
                </ul>
            </div>

            <div class="warning">
                <strong>تحذير أمني:</strong>
                <p>إذا لم تكن أنت من طلب إعادة تعيين كلمة المرور، يرجى تغيير كلمة المرور الخاصة بك فوراً والاتصال بفريق الدعم.</p>
            </div>

            <p>شكراً لك،<br>
            فريق نظام إدارة الفواتير</p>
        </div>

        <div class="footer">
            <p>هذا البريد الإلكتروني تم إرساله تلقائياً، يرجى عدم الرد عليه</p>
            <p>© {{ date('Y') }} نظام إدارة الفواتير. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>
</html>
