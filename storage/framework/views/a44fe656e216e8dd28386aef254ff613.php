<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
        }
        .error-container {
            text-align: center;
            max-width: 32rem;
            padding: 2rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        p {
            color: #6b7280;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #4338ca;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>Server Error</h1>
        <p>Sorry, something went wrong on our end. Please try again later or contact support if the problem persists.</p>
        <a href="<?php echo e(url('/')); ?>" class="btn">Return Home</a>
    </div>
</body>
</html> <?php /**PATH D:\FSQTAR-PROJECTS\salon-multi-options\resources\views\errors\500.blade.php ENDPATH**/ ?>