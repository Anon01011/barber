@extends('layouts.guest')

@section('title', 'Book Appointment - Guest')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/css/intlTelInput.css">
    <style>
        .iti {
            width: 100%;
        }

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
            --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            --shadow-medium: 0 12px 40px 0 rgba(31, 38, 135, 0.25);
            --shadow-dark: 0 8px 3y2px 0 rgba(0, 0, 0, 0.37);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            color: #333;
            overflow-x: hidden;
        }

        /* Loading Spinner */
        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top: 3px solid white;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 2rem auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Skeleton Loader */
        .skeleton {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.1) 25%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0.1) 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
            border-radius: 12px;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .glass-morphism {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
        }

        /* Enhanced Header */
        .salon-header {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0.7) 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-dark);
            position: relative;
            overflow: hidden;
        }

        .salon-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .salon-info {
            animation: fadeInLeft 0.8s ease-out;
        }

        .salon-logo {
            background: linear-gradient(135deg, #667eea, #764ba2);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 700;
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .feature-badge {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 0.75rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .feature-badge:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        /* Enhanced Slider */
        .slider {
            position: relative;
            width: 100%;
            height: 350px;
            overflow: hidden;
            border-radius: 16px;
            box-shadow: var(--shadow-medium);
            animation: fadeInRight 0.8s ease-out;
        }

        .slider img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
        }

        .slider img.active {
            opacity: 1;
        }

        /* Tab Styles */
        .tab-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 0.5rem;
            margin: 2rem auto;
            max-width: 600px;
            box-shadow: var(--shadow-light);
            display: flex;
            gap: 0.5rem;
        }

        .tab-button {
            flex: 1;
            padding: 0.625rem 1.25rem;
            border: none;
            background: transparent;
            color: rgba(0, 0, 0, 0.7);
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 1rem;
        }

        .tab-button.active {
            background: rgba(255, 255, 255, 0.25);
            color: --();
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
        }

        .tab-button:hover:not(.active) {
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.9);
        }

        /* Content Container */
        .content-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            margin: 1rem auto;
            padding: 1.5rem;
            box-shadow: var(--shadow-medium);
            max-width: 1400px;
        }

        /* Category Sidebar */
        .category-sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: var(--shadow-light);
            position: sticky;
            top: 2rem;
        }

        .category-item {
            padding: 0.5rem 0.75rem;
            margin-bottom: 0.375rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.875rem;
            position: relative;
            overflow: hidden;
        }

        .category-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--accent-gradient);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .category-item:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateX(8px);
        }

        .category-item.active {
            color: #2dd4bf;
            /* Teal color to match image */
            font-weight: 600;
            background: transparent;
            box-shadow: none;
        }

        .category-item.active::before {
            background: #2dd4bf;
            transform: scaleY(1);
        }

        /* Service List Item Styles */
        .service-list-item {
            background: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0.75rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .service-list-item:last-child {
            border-bottom: none;
        }

        .service-list-item:hover {
            background: rgba(255, 255, 255, 0.5);
        }

        .service-info {
            flex: 1;
        }

        .service-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.25rem;
        }

        .service-meta {
            font-size: 0.9rem;
            color: #666;
        }

        .service-action {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .service-price {
            font-size: 1rem;
            font-weight: 700;
            color: #333;
        }

        .add-btn {
            background: white;
            border: 1px solid #2dd4bf;
            color: #2dd4bf;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            cursor: pointer;
            min-width: 80px;
        }

        .add-btn:hover {
            background: #2dd4bf;
            color: white;
        }

        /* Sticky Layout */
        .sticky-sidebar {
            position: sticky;
            top: 2rem;
            height: fit-content;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
        }

        /* Category Header in List */
        .service-category-header {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            padding-bottom: 0.25rem;
            border-bottom: 1px solid #eee;
        }

        .service-category-header:first-child {
            margin-top: 0;
        }

        /* Scroll Spy Active State */
        .category-item.active {
            background-color: rgba(45, 212, 191, 0.1);
            color: #2dd4bf;
            border-right: 3px solid #2dd4bf;
        }

        /* Booking Options Styles */
        .booking-option-section {
            margin-bottom: 2rem;
        }

        .booking-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }

        /* Staff Radio Styles */
        .staff-radio-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }

        .staff-radio-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-size: 0.95rem;
            color: #555;
        }

        .staff-radio-input {
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #ddd;
            border-radius: 50%;
            position: relative;
            transition: all 0.2s;
        }

        .staff-radio-input:checked {
            border-color: #2dd4bf;
        }

        .staff-radio-input:checked::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 10px;
            height: 10px;
            background: #2dd4bf;
            border-radius: 50%;
        }

        /* Date Card Styles */
        .date-scroll-container {
            display: flex;
            gap: 0.75rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            scrollbar-width: thin;
        }

        .date-card {
            min-width: 100px;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 0.75rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .date-card:hover {
            border-color: #2dd4bf;
        }

        .date-card.active {
            background: #2dd4bf;
            color: white;
            border-color: #2dd4bf;
        }

        .date-day {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .date-month {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        /* Time Grid Styles */
        .time-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.75rem;
        }

        .time-slot-btn {
            border: 1px solid #ddd;
            background: white;
            padding: 0.5rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
            cursor: pointer;
            transition: all 0.2s;
        }

        .time-slot-btn:hover {
            border-color: #2dd4bf;
            color: #2dd4bf;
        }

        .time-slot-btn.active {
            background: #2dd4bf;
            color: white;
            border-color: #2dd4bf;
        }

        .time-section-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.75rem;
            margin-top: 1rem;
        }

        /* Enhanced Cart Section */
        .cart-section {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.98) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: var(--shadow-medium);
            position: sticky;
            top: 2rem;
        }

        .cart-header {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cart-empty {
            text-align: center;
            padding: 2rem 0.75rem;
        }

        .cart-empty-icon {
            font-size: 3rem;
            margin-bottom: 0.75rem;
            opacity: 0.3;
        }

        .cart-item {
            background: rgba(102, 126, 234, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(102, 126, 234, 0.1);
            border-radius: 10px;
            padding: 0.875rem;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            animation: slideInRight 0.4s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .cart-item:hover {
            transform: translateX(-4px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.15);
        }

        .cart-item-name {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.375rem;
            font-size: 1rem;
        }

        .cart-item-price {
            color: #667eea;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .quantity-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn:hover {
            background: #667eea;
            color: white;
            transform: scale(1.1);
        }

        .quantity-value {
            font-weight: 700;
            min-width: 24px;
            text-align: center;
        }

        .remove-btn {
            color: #f5576c;
            background: rgba(245, 87, 108, 0.1);
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-btn:hover {
            background: #f5576c;
            color: white;
            transform: scale(1.1);
        }

        .cart-total {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid rgba(102, 126, 234, 0.2);
        }

        .total-label {
            font-size: 1rem;
            font-weight: 600;
            color: #666;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 800;
            background: var(--secondary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .proceed-btn {
            background: linear-gradient(135deg, #2dd4bf 0%, #14b8a6 100%);
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 1.5rem;
            box-shadow: 0 4px 15px rgba(45, 212, 191, 0.4);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .proceed-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .proceed-btn:hover::before {
            width: 400px;
            height: 400px;
        }

        .proceed-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(45, 212, 191, 0.6);
        }

        /* Enhanced Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 700px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp 0.4s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 1.25rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.375rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .close-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            background: rgba(0, 0, 0, 0.05);
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .close-btn:hover {
            background: rgba(0, 0, 0, 0.1);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 1.25rem;
        }

        .form-section {
            margin-bottom: 1.25rem;
        }

        .section-title {
            font-size: 1.0625rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--accent-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.125rem;
        }

        .form-group {
            margin-bottom: 0.875rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.375rem;
            font-size: 0.9375rem;
        }

        .required {
            color: #f5576c;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 0.625rem 0.875rem;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            font-size: 0.9375rem;
            transition: all 0.3s ease;
            background: white;
            font-family: 'Inter', sans-serif;
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-control::placeholder {
            color: #999;
        }

        .service-summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: rgba(102, 126, 234, 0.05);
            border-radius: 8px;
            margin-bottom: 0.75rem;
        }

        .service-summary-name {
            font-weight: 600;
            color: #1a1a1a;
        }

        .service-summary-price {
            font-weight: 700;
            color: #667eea;
        }

        .modal-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn-secondary {
            padding: 0.625rem 1.5rem;
            border: 2px solid #667eea;
            background: white;
            color: #667eea;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-secondary:hover {
            background: rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .btn-primary {
            padding: 0.625rem 1.75rem;
            border: none;
            background: var(--secondary-gradient);
            color: white;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
            font-size: 1rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 87, 108, 0.6);
        }

        /* Success Modal */
        .success-modal {
            text-align: center;
            padding: 3rem 2rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--success-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem;
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-title {
            font-size: 2rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 1rem;
        }

        .success-message {
            font-size: 1.125rem;
            color: #666;
            margin-bottom: 2rem;
        }

        /* Animations */
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            #services-section {
                grid-template-columns: 1fr;
            }

            .content-container {
                margin: 1rem;
                padding: 1.5rem;
                border-radius: 20px;
            }

            .tab-container {
                margin: 1rem;
                padding: 0.375rem;
            }

            .tab-button {
                padding: 0.75rem 1rem;
                font-size: 0.9375rem;
            }

            .service-card {
                margin-bottom: 1rem;
            }

            .slider {
                height: 250px;
            }

            .cart-section {
                position: static;
                margin-top: 2rem;
            }

            .modal-content {
                max-height: 95vh;
                border-radius: 20px 20px 0 0;
            }

            .modal-footer {
                flex-direction: column;
            }

            .btn-secondary,
            .btn-primary {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .salon-logo {
                width: 48px;
                height: 48px;
                font-size: 20px;
            }

            .slider {
                height: 200px;
            }

            .service-name {
                font-size: 1.125rem;
            }

            .service-price {
                font-size: 1.25rem;
            }

            .modal-title {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1.125rem;
            }
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-gradient);
        }

        /* Custom Select Dropdown */
        .custom-select-container {
            position: relative;
            width: 100%;
        }

        .custom-select-display {
            padding: 0.625rem 0.875rem;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background: white;
            cursor: pointer;
            font-size: 0.9375rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 42px;
            transition: all 0.3s ease;
        }

        .custom-select-display:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .custom-select-display::after {
            content: '▼';
            margin-left: auto;
            font-size: 0.75rem;
            color: #666;
            transition: transform 0.3s ease;
        }

        .custom-select-display.open::after {
            transform: rotate(180deg);
        }

        .custom-select-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 2px solid #667eea;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1001;
            list-style: none;
            padding: 0;
            margin: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .custom-select-options li {
            padding: 0.625rem 0.875rem;
            cursor: pointer;
            transition: background 0.2s ease;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .custom-select-options li:last-child {
            border-bottom: none;
        }

        .custom-select-options li:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        .custom-select-options li.selected {
            background: rgba(102, 126, 234, 0.2);
            font-weight: 600;
        }

        /* Sticky Layout */
        .sticky-sidebar {
            position: sticky;
            top: 2rem;
            height: fit-content;
            max-height: calc(100vh - 4rem);
            overflow-y: auto;
        }

        /* Category Header in List */
        .service-category-header {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-top: 2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eee;
        }

        .service-category-header:first-child {
            margin-top: 0;
        }

        /* Scroll Spy Active State */
        .category-item.active {
            background-color: rgba(45, 212, 191, 0.1);
            color: #2dd4bf;
            border-right: 3px solid #2dd4bf;
        }
    </style>
@endpush

@section('content')
    @if(isset($guestBookingEnabled) && !$guestBookingEnabled)
        <!-- Booking Disabled Message -->
        <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500">
            <div
                class="bg-white/95 backdrop-blur-lg rounded-2xl p-12 max-w-md mx-4 text-center shadow-2xl border border-white/20">
                <div class="text-6xl mb-6">📅</div>
                <h1 class="text-2xl font-bold text-gray-800 mb-4">Booking Unavailable</h1>
                <p class="text-gray-600 mb-6 leading-relaxed">Booking is not available for now. Please contact us directly to
                    schedule your appointment.</p>
                <div class="flex flex-col gap-3">
                    <a href="tel:{{ $business_phone ?? '+974 33102532' }}"
                        class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-full font-semibold hover:shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-phone"></i> Call Now
                    </a>
                    <a href="mailto:{{ $business_email ?? 'info@salon.com' }}"
                        class="bg-white text-gray-700 px-6 py-3 rounded-full font-semibold border-2 border-gray-300 hover:border-gray-400 transition-all duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-envelope"></i> Email Us
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Enhanced Header Section -->
        <header class="salon-header text-white w-full">
            <div class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- Salon Info -->
                <div class="salon-info space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="salon-logo overflow-hidden flex items-center justify-center text-white font-bold text-xl">
                            @if(!empty($business_logo))
                                <img src="{{ asset('storage/' . $business_logo) }}" alt="Logo" class="w-full h-full object-cover">
                            @else
                                {{ substr($business_name ?? 'Salon', 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">{{ $business_name ?? 'Salon Name' }}</h1>
                            <p class="text-gray-300 text-sm mt-1">{{ $business_address ?? 'Your Beauty Destination' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-phone text-cyan-400"></i>
                            <span>{{ $business_phone ?? '+974 33102532' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-clock text-cyan-400"></i>
                            <span>{{ $formatted_hours ?? '9:00 AM - 9:00 PM' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Salon Slider -->
                <div class="slider">
                    <img src="https://images.unsplash.com/photo-1560066984-138dadb4c035?w=800&h=600&fit=crop"
                        alt="Salon Interior" class="active">
                    <img src="https://images.unsplash.com/photo-1559599101-f09722fb4948?w=800&h=600&fit=crop"
                        alt="Hair Styling">
                    <img src="https://images.unsplash.com/photo-1616394584738-fc6e612e71b9?w=800&h=600&fit=crop"
                        alt="Beauty Treatment">
                    <img src="https://images.unsplash.com/photo-1570172619644-dfd03ed5d881?w=800&h=600&fit=crop"
                        alt="Spa Experience">
                </div>
            </div>

            <!-- Feature Badges -->
            <div class="bg-white text-black py-6 grid grid-cols-1 md:grid-cols-3 gap-4 px-6 max-w mx-auto">
                <div class="feature-badge text-center">
                    <div class="text-3xl mb-2">⚡</div>
                    <strong class="block text-lg">Instant Booking</strong>
                    <span class="text-gray-600 text-sm">No queue at store</span>
                </div>
                <div class="feature-badge text-center">
                    <div class="text-3xl mb-2">📝</div>
                    <strong class="block text-lg">Book Now</strong>
                    <span class="text-gray-600 text-sm">Pay at store</span>
                </div>
                <div class="feature-badge text-center">
                    <div class="text-3xl mb-2">👤</div>
                    <strong class="block text-lg">Guest Booking</strong>
                    <span class="text-gray-600 text-sm">No account needed</span>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-container">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Category Sidebar (3 cols) -->
                <aside id="category-sidebar-container" class="lg:col-span-3 hidden lg:block">
                    <div class="category-sidebar sticky-sidebar">
                        <h3 class="text-xl font-bold mb-4 text-gray-900 border-b pb-2">
                            Services
                        </h3>
                        <ul class="space-y-1" id="category-list">
                            <!-- All Services is implicit now -->
                            @foreach($serviceCategories as $category)
                                <li class="category-item" data-category="{{ $category->id }}">
                                    {{ $category->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </aside>

                <!-- Services List (6 cols) -->
                <section class="lg:col-span-6" id="services-section">
                    <div class="spinner"></div>
                </section>

                <!-- Booking Options Section (Hidden by default) -->
                <section class="lg:col-span-6" id="booking-options-section" style="display: none;">
                    <!-- Staff Selection -->
                    <div class="booking-option-section">
                        <h4 class="booking-section-title">Select Staff</h4>
                        <div class="staff-radio-group" id="staff-selection-container">
                            <label class="staff-radio-label">
                                <input type="radio" name="staff_selection" value="" class="staff-radio-input" checked>
                                Any Staff
                            </label>
                            @foreach($staffMembers as $staff)
                                <label class="staff-radio-label staff-option" data-staff-id="{{ $staff->id }}">
                                    <input type="radio" name="staff_selection" value="{{ $staff->id }}" class="staff-radio-input">
                                    {{ $staff->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="booking-option-section">
                        <h4 class="booking-section-title">Select Date</h4>
                        <div class="date-scroll-container" id="date-selection-container">
                            <!-- Dates will be populated by JS -->
                        </div>
                        <button class="text-sm text-gray-500 mt-2 hover:text-teal-500 flex items-center gap-1"
                            id="show-more-dates">
                            Show More Dates <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    <!-- Time Selection -->
                    <div class="booking-option-section">
                        <h4 class="booking-section-title">Select Time</h4>
                        <div id="time-selection-container">
                            <div class="text-center text-gray-500 py-4">Please select a date to view available times</div>
                        </div>
                    </div>
                </section>

                <!-- Cart Section (3 cols) - Sticky -->
                <aside class="lg:col-span-3 cart-section sticky-sidebar">
                    <h3 class="cart-header">
                        <i class="fas fa-shopping-cart mr-2"></i>Your Cart
                    </h3>
                    <div id="cart-empty" class="cart-empty">
                        <div class="cart-empty-icon">🛒</div>
                        <p class="font-semibold text-gray-700 mb-2">Your Cart is Empty</p>
                        <p class="text-gray-500 text-sm">Select services to get started</p>
                    </div>
                    <div id="cart-items" style="display: none;"></div>
                    <div id="cart-total" style="display: none;" class="cart-total">
                        <div class="flex justify-between items-center mb-4">
                            <span class="total-label">Total:</span>
                            <span class="total-amount">{{ $currency_symbol ?? '$' }} <span id="total-amount">0.00</span></span>
                        </div>
                        <button id="proceed-booking" class="proceed-btn">
                            <i class="fas fa-calendar-check mr-2"></i>Book Appointment
                        </button>
                    </div>
                </aside>
            </div>
        </div>

        <!-- My Booking Section -->
        <div id="my-booking-section" class="max-w-3xl mx-auto" style="display: none;">
            <!-- Search Form -->
            <div class="bg-white rounded-2xl p-8 shadow-lg mb-6">
                <h2 class="text-2xl font-bold mb-6 text-center"
                    style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <i class="fas fa-search mr-2"></i>Check Your Appointment
                </h2>
                <p class="text-gray-600 text-center mb-6">Enter your email or phone number to view your existing appointments
                </p>

                <form id="check-appointment-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="form-group">
                            <label for="check-phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="check-phone" name="phone" placeholder="33102532">
                            <input type="hidden" id="check_country_code" name="country_code">
                        </div>
                        <div class="form-group">
                            <label for="check-email" class="form-label">Email (Optional)</label>
                            <input type="email" class="form-control" id="check-email" name="email"
                                placeholder="john@example.com">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-search mr-2"></i>Search Appointments
                    </button>
                </form>
            </div>

            <!-- Search Results -->
            <div id="appointment-results" style="display: none;">
                <h3 class="text-xl font-bold mb-4"
                    style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <i class="fas fa-calendar-alt mr-2"></i>Your Appointments
                </h3>
                <div id="appointments-list"></div>
            </div>

            <!-- No Results Message -->
            <div id="no-appointments" class="bg-white rounded-2xl p-8 text-center" style="display: none;">
                <div class="text-6xl mb-4 opacity-30">📅</div>
                <h3 class="text-xl font-bold text-gray-700 mb-2">No Appointments Found</h3>
                <p class="text-gray-500">We couldn't find any appointments with the provided information.</p>
            </div>
        </div>
        </div>

        <!-- Booking Modal -->
        <div id="booking-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i class="fas fa-calendar-check mr-2"></i>Complete Your Booking
                    </h2>
                    <button id="close-modal" class="close-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="booking-form" action="{{ route('booking.guest.store') }}" method="POST">
                    @csrf
                    <input type="hidden" id="hidden-date" name="date">
                    <input type="hidden" id="hidden-time" name="time">
                    <input type="hidden" id="hidden-staff" name="staff_id">
                    <input type="hidden" id="hidden-name" name="name">
                    <input type="hidden" id="hidden-services" name="services_json">
                    <div class="modal-body">
                        <div class="form-section border-0 p-0">
                            <h3 class="text-xl font-bold mb-6 text-gray-800">Enter Your Details</h3>

                            <div class="grid grid-cols-1 gap-4">
                                <div class="form-group">
                                    <label for="first_name" class="form-label text-gray-600 text-sm">First Name</label>
                                    <input type="text"
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none"
                                        id="first_name" name="first_name" placeholder="John" required>
                                </div>

                                <div class="form-group">
                                    <label for="last_name" class="form-label text-gray-600 text-sm">Last Name</label>
                                    <input type="text"
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none"
                                        id="last_name" name="last_name" placeholder="Doe" required>
                                </div>

                                <div class="form-group">
                                    <label for="email" class="form-label text-gray-600 text-sm">Email Address</label>
                                    <input type="email"
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none"
                                        id="email" name="email" placeholder="john@example.com">
                                </div>

                                <div class="form-group">
                                    <label for="phone" class="form-label text-gray-600 text-sm">Mobile Number <span
                                            class="text-red-500">*</span></label>
                                    <input type="tel"
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none"
                                        id="phone" name="phone" placeholder="3310253212" required>
                                    <input type="hidden" id="country_code" name="country_code">
                                </div>

                                <div class="form-group">
                                    <label for="address" class="form-label text-gray-600 text-sm">Address</label>
                                    <textarea
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none resize-none"
                                        id="address" name="address" rows="2" placeholder="Your address"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="modal-notes" class="form-label text-gray-600 text-sm">Booking Notes</label>
                                    <textarea
                                        class="form-control border-b border-gray-200 focus:border-blue-500 px-0 rounded-none resize-none"
                                        id="modal-notes" name="notes" rows="2" placeholder="Any special requests?"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-t-0 px-6 pb-6 pt-0">
                        <button type="submit"
                            class="btn-primary w-full rounded-full py-3 text-lg font-semibold shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5">
                            Submit Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Success Modal -->
        <div id="success-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="success-modal">
                    <div class="success-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2 class="success-title">Booking Confirmed!</h2>
                    <p class="success-message">Your appointment has been successfully booked. We'll contact you soon to confirm.
                    </p>
                    <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <p class="text-sm text-gray-500 mb-1">Booking Reference</p>
                        <p class="text-xl font-bold text-gray-800">#<span id="success-booking-id">-</span></p>
                    </div>
                    <button id="close-success" class="btn-primary">
                        <i class="fas fa-home mr-2"></i>Back to Home
                    </button>
                </div>
            </div>
        </div>

        <!-- Reschedule Modal -->
        <div id="reschedule-modal" class="modal-overlay" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">
                        <i class="fas fa-calendar-alt mr-2"></i>Reschedule Appointment
                    </h2>
                    <button id="close-reschedule" class="close-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form id="reschedule-form" action="" method="POST">
                    @csrf
                    <input type="hidden" id="reschedule-booking-id" name="booking_id">
                    <div class="modal-body">
                        <!-- Personal Information -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                Contact Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label for="reschedule-phone" class="form-label">Phone Number <span
                                            class="required">*</span></label>
                                    <input type="tel" class="form-control" id="reschedule-phone" name="phone"
                                        placeholder="33102532" required>
                                    <input type="hidden" id="reschedule_country_code" name="country_code">
                                </div>
                                <div class="form-group">
                                    <label for="reschedule-email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="reschedule-email" name="email"
                                        placeholder="john@example.com">
                                </div>
                            </div>
                        </div>

                        <!-- Service Selection -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-cut"></i>
                                </div>
                                Service
                            </h3>
                            <div class="form-group">
                                <select class="form-select" id="reschedule-service" name="service_id" required>
                                    <option value="">Select a service</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                New Date & Time
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label for="reschedule-date" class="form-label">Date <span class="required">*</span></label>
                                    <input type="date" class="form-control" id="reschedule-date" name="date"
                                        min="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="reschedule-time" class="form-label">Time <span class="required">*</span></label>
                                    <input type="time" class="form-control" id="reschedule-time" name="time" required>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Selection -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <div class="section-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                Preferred Staff
                            </h3>
                            <div class="form-group">
                                <select class="form-select" id="reschedule-staff" name="staff_id">
                                    <option value="">Any available staff</option>
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="cancel-reschedule" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-calendar-check mr-2"></i>Reschedule
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@19.5.6/build/js/intlTelInput.min.js"></script>
            <script src="{{ asset('js/customer-phone-handler.js') }}?v={{ time() }}"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    try {


                        // --- State Variables ---
                        let cart = [];
                        let allServices = [];
                        let selectedCategory = 'all';
                        let currentStep = 1;
                        let selectedStaff = '';
                        let selectedDate = '';
                        let selectedTime = '';
                        let availableSlots = [];
                        let datesRendered = 0;



                        function validateEmail(email) {
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            return emailRegex.test(email);
                        }

                        function showError(fieldId, message) {
                            const field = document.getElementById(fieldId);
                            if (!field) return;
                            const errorDiv = field.parentNode.querySelector('.error-message') || document.createElement('div');
                            errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                            errorDiv.textContent = message;
                            if (!field.parentNode.querySelector('.error-message')) {
                                field.parentNode.appendChild(errorDiv);
                            }
                            field.classList.add('border-red-500');
                        }

                        function clearError(fieldId) {
                            const field = document.getElementById(fieldId);
                            if (!field) return;
                            const errorDiv = field.parentNode.querySelector('.error-message');
                            if (errorDiv) errorDiv.remove();
                            field.classList.remove('border-red-500');
                        }

                        // --- Booking Form Submission (Critical) ---
                        const bookingForm = document.getElementById('booking-form');

                        if (bookingForm) {
                            bookingForm.addEventListener('submit', function (e) {

                                e.preventDefault();

                                // Clear previous errors
                                clearError('phone');
                                clearError('email');

                                const phoneEl = document.getElementById('phone');
                                const emailEl = document.getElementById('email');
                                const phone = phoneEl ? phoneEl.value.trim() : '';
                                const email = emailEl ? emailEl.value.trim() : '';

                                let hasError = false;

                                // Validate phone
                                if (!phone) {
                                    showError('phone', 'Phone number is required.');
                                    hasError = true;
                                } else if (window.customerPhoneHandler && !window.customerPhoneHandler.validatePhoneNumber(window.itiPhone, phoneEl, false)) {
                                    showError('phone', 'Please enter a valid phone number.');
                                    hasError = true;
                                } else if (window.customerPhoneHandler) {
                                    // Update to E.164
                                    phoneEl.value = window.customerPhoneHandler.getFullNumber(phoneEl);
                                }

                                // Validate email if provided
                                if (email && !validateEmail(email)) {
                                    showError('email', 'Please enter a valid email address.');
                                    hasError = true;
                                }

                                // Validate date and time (from inline selection)
                                if (!selectedDate) {
                                    alert('Date is required.');
                                    hasError = true;
                                }
                                if (!selectedTime) {
                                    alert('Time is required.');
                                    hasError = true;
                                }

                                if (hasError) return;

                                // Show loading state
                                const submitBtn = this.querySelector('button[type="submit"]');
                                const originalText = submitBtn.innerHTML;
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
                                submitBtn.disabled = true;

                                // Update hidden fields before creating FormData
                                const firstNameEl = document.getElementById('first_name');
                                const lastNameEl = document.getElementById('last_name');
                                const firstName = firstNameEl ? firstNameEl.value.trim() : '';
                                const lastName = lastNameEl ? lastNameEl.value.trim() : '';
                                const hiddenName = document.getElementById('hidden-name');
                                if (hiddenName) hiddenName.value = `${firstName} ${lastName}`;
                                const hiddenDate = document.getElementById('hidden-date');
                                const hiddenTime = document.getElementById('hidden-time');
                                const hiddenStaff = document.getElementById('hidden-staff');
                                const hiddenServices = document.getElementById('hidden-services');

                                if (hiddenDate) hiddenDate.value = selectedDate;
                                if (hiddenTime) hiddenTime.value = selectedTime;
                                if (hiddenStaff) hiddenStaff.value = selectedStaff;
                                if (hiddenServices) hiddenServices.value = JSON.stringify(cart);

                                const formData = new FormData(this);

                                fetch(this.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    }
                                })
                                    .then(response => {
                                        if (response.ok) {
                                            return response.json();
                                        } else if (response.status === 422) {
                                            return response.json().then(errData => {
                                                let message = errData.message || 'Validation failed';
                                                if (errData.errors) {
                                                    message = Object.values(errData.errors).flat().join('\n');
                                                }
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        title: 'Please Check Your Input',
                                                        text: message,
                                                        icon: 'warning',
                                                        confirmButtonColor: '#000'
                                                    });
                                                } else {
                                                    alert('Booking Error: ' + message);
                                                }
                                                submitBtn.innerHTML = originalText;
                                                submitBtn.disabled = false;
                                                return Promise.reject(new Error('Validation error'));
                                            });
                                        } else if (response.status === 403) {
                                            return response.json().then(errData => {
                                                const userMessage = "We are currently unable to accept new online bookings. Please contact the salon directly for assistance.";
                                                if (typeof Swal !== 'undefined') {
                                                    Swal.fire({
                                                        title: 'Booking Unavailable',
                                                        text: userMessage,
                                                        icon: 'info',
                                                        confirmButtonColor: '#000',
                                                        confirmButtonText: 'Okay, I understand'
                                                    });
                                                } else {
                                                    alert(userMessage);
                                                }
                                                submitBtn.innerHTML = originalText;
                                                submitBtn.disabled = false;
                                                return Promise.reject(new Error('Booking limit reached'));
                                            });
                                        } else {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                    })
                                    .then(data => {
                                        if (data.success) {
                                            document.getElementById('booking-modal').style.display = 'none';
                                            if (data.booking_id) {
                                                document.getElementById('success-booking-id').textContent = data.booking_id;
                                            }
                                            document.getElementById('success-modal').style.display = 'flex';
                                            cart = [];
                                            updateCartDisplay();
                                            this.reset();
                                        } else {
                                            alert('Error: ' + (data.message || 'Failed to create booking'));
                                            submitBtn.innerHTML = originalText;
                                            submitBtn.disabled = false;
                                        }
                                    })
                                    .catch(error => {

                                        if (!error.message.includes('Validation error') && !error.message.includes('Booking limit reached')) {
                                            alert('Failed to create booking. Please try again.');
                                        }
                                        submitBtn.innerHTML = originalText;
                                        submitBtn.disabled = false;
                                    });
                            });
                        }

                        // Initialize Intl Tel Input via CustomerPhoneHandler
                        if (window.customerPhoneHandler) {
                            window.customerPhoneHandler.init();

                            // Map instances for existing logic
                            const phoneInput = document.querySelector("#phone");
                            const checkPhoneInput = document.querySelector("#check-phone");
                            const reschedulePhoneInput = document.querySelector("#reschedule-phone");

                            if (phoneInput) {
                                const phoneData = window.customerPhoneHandler.phoneInputs.find(p => p.input === phoneInput);
                                if (phoneData) window.itiPhone = phoneData.iti;
                            }

                            if (checkPhoneInput) {
                                const checkPhoneData = window.customerPhoneHandler.phoneInputs.find(p => p.input === checkPhoneInput);
                                if (checkPhoneData) window.itiCheckPhone = checkPhoneData.iti;
                            }

                            if (reschedulePhoneInput) {
                                const reschedulePhoneData = window.customerPhoneHandler.phoneInputs.find(p => p.input === reschedulePhoneInput);
                                if (reschedulePhoneData) window.itiReschedulePhone = reschedulePhoneData.iti;
                            }
                        }

                        // Slider functionality
                        let currentSlide = 0;
                        const slides = document.querySelectorAll('.slider img');
                        const totalSlides = slides.length;

                        function showSlide(index) {
                            slides.forEach(slide => slide.classList.remove('active'));
                            slides[index].classList.add('active');
                        }

                        function nextSlide() {
                            currentSlide = (currentSlide + 1) % totalSlides;
                            showSlide(currentSlide);
                        }

                        setInterval(nextSlide, 4000);
                        showSlide(0);





                        // Check appointment form submission
                        const checkAppointmentForm = document.getElementById('check-appointment-form');
                        if (checkAppointmentForm) {
                            checkAppointmentForm.addEventListener('submit', function (e) {
                                e.preventDefault();

                                const phone = document.getElementById('check-phone').value.trim();
                                const email = document.getElementById('check-email').value.trim();

                                // Clear previous errors
                                clearError('check-phone');
                                clearError('check-email');

                                let hasError = false;

                                // Check if at least one field is provided
                                if (!phone && !email) {
                                    showError('check-phone', 'Please enter at least a phone number or email address.');
                                    hasError = true;
                                }

                                // Validate phone if provided
                                if (phone && window.customerPhoneHandler && !window.customerPhoneHandler.validatePhoneNumber(window.itiCheckPhone, document.getElementById('check-phone'), false)) {
                                    showError('check-phone', 'Please enter a valid phone number.');
                                    hasError = true;
                                } else if (phone && window.customerPhoneHandler) {
                                    // Update to E.164
                                    document.getElementById('check-phone').value = window.customerPhoneHandler.getFullNumber(document.getElementById('check-phone'));
                                }

                                // Validate email if provided
                                if (email && !validateEmail(email)) {
                                    showError('check-email', 'Please enter a valid email address.');
                                    hasError = true;
                                }

                                if (hasError) return;

                                // Show loading state
                                const submitBtn = this.querySelector('button[type="submit"]');
                                const originalText = submitBtn.innerHTML;
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Searching...';
                                submitBtn.disabled = true;

                                // Prepare form data
                                const formData = new FormData();
                                if (phone) formData.append('phone', phone);
                                if (email) formData.append('email', email);

                                fetch('{{ route('booking.guest.check') }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        submitBtn.innerHTML = originalText;
                                        submitBtn.disabled = false;

                                        if (data.bookings && data.bookings.length > 0) {
                                            displayAppointments(data.bookings, data.customer);
                                        } else {
                                            document.getElementById('appointment-results').style.display = 'none';
                                            document.getElementById('no-appointments').style.display = 'block';
                                        }
                                    })
                                    .catch(error => {

                                        alert('Failed to search appointments. Please try again.');
                                        submitBtn.innerHTML = originalText;
                                        submitBtn.disabled = false;
                                    });
                            });
                        }

                        // Display appointments function
                        function displayAppointments(bookings, customer) {
                            const appointmentsList = document.getElementById('appointments-list');
                            appointmentsList.innerHTML = '';

                            bookings.forEach(booking => {
                                const statusColors = {
                                    'pending': 'bg-yellow-100 text-yellow-800',
                                    'confirmed': 'bg-green-100 text-green-800',
                                    'completed': 'bg-blue-100 text-blue-800',
                                    'cancelled': 'bg-red-100 text-red-800'
                                };

                                const appointmentCard = document.createElement('div');
                                appointmentCard.className = 'bg-white rounded-xl p-6 mb-4 shadow-md hover:shadow-lg transition-all';
                                appointmentCard.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="flex justify-between items-start mb-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <h4 class="text-lg font-bold text-gray-900">${booking.service}</h4>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <p class="text-sm text-gray-600 mt-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fas fa-user-tie mr-1"></i>Staff: ${booking.staff}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusColors[booking.status] || 'bg-gray-100 text-gray-800'}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="grid grid-cols-2 gap-4 mb-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <p class="text-sm text-gray-500">Date</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <p class="font-semibold text-gray-900">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fas fa-calendar mr-1 text-purple-600"></i>${booking.date}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <p class="text-sm text-gray-500">Time</p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <p class="font-semibold text-gray-900">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fas fa-clock mr-1 text-purple-600"></i>${booking.time}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </p>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ${booking.status === 'pending' || booking.status === 'confirmed' ? `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <button onclick="rescheduleAppointment(this)"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-booking-id="${booking.id}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-phone="${customer.phone}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-country-code="${customer.country_code || ''}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-email="${customer.email || ''}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-service-id="${booking.service_id}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-date="${booking.date}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-time="${booking.time}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            data-staff-id="${booking.staff_id || ''}"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            class="btn-secondary w-full">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <i class="fas fa-calendar-alt mr-2"></i>Reschedule
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            `;
                                appointmentsList.appendChild(appointmentCard);
                            });

                            document.getElementById('appointment-results').style.display = 'block';
                            document.getElementById('no-appointments').style.display = 'none';
                        }

                        // Reschedule appointment function
                        window.rescheduleAppointment = function (button) {
                            const bookingId = button.dataset.bookingId;
                            const phone = button.dataset.phone;
                            const countryCode = button.dataset.countryCode;
                            const email = button.dataset.email;
                            const serviceId = button.dataset.serviceId;
                            const date = button.dataset.date;
                            const time = button.dataset.time;
                            const staffId = button.dataset.staffId;

                            // Populate reschedule form
                            const reschedulePhoneInput = document.getElementById('reschedule-phone');
                            if (window.itiReschedulePhone) {
                                if (countryCode) {
                                    window.itiReschedulePhone.setNumber(countryCode + phone);
                                } else {
                                    window.itiReschedulePhone.setNumber(phone);
                                }
                            } else {
                                reschedulePhoneInput.value = phone;
                            }
                            document.getElementById('reschedule-email').value = email;
                            document.getElementById('reschedule-service').value = serviceId;
                            document.getElementById('reschedule-date').value = date;
                            document.getElementById('reschedule-time').value = time;
                            document.getElementById('reschedule-staff').value = staffId || '';
                            document.getElementById('reschedule-booking-id').value = bookingId;

                            // Show reschedule modal
                            document.getElementById('reschedule-modal').style.display = 'flex';
                        };

                        // Handle reschedule form submission
                        const rescheduleForm = document.getElementById('reschedule-form');
                        if (rescheduleForm) {
                            rescheduleForm.addEventListener('submit', function (e) {
                                e.preventDefault();

                                const submitBtn = this.querySelector('button[type="submit"]');
                                const originalText = submitBtn.innerHTML;
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Rescheduling...';
                                submitBtn.disabled = true;

                                // Validate phone
                                const reschedulePhoneEl = document.getElementById('reschedule-phone');
                                if (window.customerPhoneHandler && !window.customerPhoneHandler.validatePhoneNumber(window.itiReschedulePhone, reschedulePhoneEl, false)) {
                                    alert('Please enter a valid phone number.');
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                    return;
                                } else if (window.customerPhoneHandler) {
                                    reschedulePhoneEl.value = window.customerPhoneHandler.getFullNumber(reschedulePhoneEl);
                                }

                                const formData = new FormData(this);
                                const bookingId = document.getElementById('reschedule-booking-id').value;


                                const rescheduleUrl = "{{ route('guest.booking.reschedule', ['salon_slug' => app()->bound('current_salon') ? app('current_salon')->slug : 'PLACEHOLDER_SLUG', 'id' => 'PLACEHOLDER_ID']) }}";
                                fetch(rescheduleUrl.replace('PLACEHOLDER_ID', bookingId), {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                    }
                                })
                                    .then(response => {
                                        if (response.ok) {
                                            return response.json();
                                        } else if (response.status === 422) {
                                            return response.json().then(errData => {
                                                let message = errData.message || 'Validation failed';
                                                if (errData.errors) {
                                                    message = Object.values(errData.errors).flat().join('\n');
                                                }
                                                alert('Reschedule Error: ' + message);
                                                submitBtn.innerHTML = originalText;
                                                submitBtn.disabled = false;
                                                return Promise.reject(new Error('Validation error'));
                                            });
                                        } else {
                                            throw new Error(`HTTP error! status: ${response.status}`);
                                        }
                                    })
                                    .then(data => {
                                        if (data.success) {
                                            // Hide reschedule modal
                                            document.getElementById('reschedule-modal').style.display = 'none';

                                            // Show success modal
                                            document.getElementById('success-modal').style.display = 'flex';

                                            // Refresh appointments
                                            const phone = document.getElementById('check-phone').value.trim();
                                            const email = document.getElementById('check-email').value.trim();
                                            const formDataRefresh = new FormData();
                                            if (phone) formDataRefresh.append('phone', phone);
                                            if (email) formDataRefresh.append('email', email);

                                            fetch('{{ route('booking.guest.check') }}', {
                                                method: 'POST',
                                                body: formDataRefresh,
                                                headers: {
                                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                    'Accept': 'application/json',
                                                }
                                            })
                                                .then(response => response.json())
                                                .then(refreshData => {
                                                    if (refreshData.bookings && refreshData.bookings.length > 0) {
                                                        displayAppointments(refreshData.bookings, refreshData.customer);
                                                    }
                                                });
                                        } else {
                                            alert('Error: ' + (data.message || 'Failed to reschedule booking'));
                                            submitBtn.innerHTML = originalText;
                                            submitBtn.disabled = false;
                                        }
                                    })
                                    .catch(error => {

                                        if (!error.message.includes('Validation error')) {
                                            alert('Failed to reschedule booking. Please try again.');
                                        }
                                        submitBtn.innerHTML = originalText;
                                        submitBtn.disabled = false;
                                    });
                            });
                        }

                        // Close reschedule modal
                        const closeReschedule = document.getElementById('close-reschedule');
                        if (closeReschedule) {
                            closeReschedule.addEventListener('click', function () {
                                document.getElementById('reschedule-modal').style.display = 'none';
                            });
                        }

                        const cancelReschedule = document.getElementById('cancel-reschedule');
                        if (cancelReschedule) {
                            cancelReschedule.addEventListener('click', function () {
                                document.getElementById('reschedule-modal').style.display = 'none';
                            });
                        }



                        // Load services on page load
                        loadServices();

                        // Category click handlers
                        const categoryList = document.getElementById('category-list');
                        if (categoryList) {
                            categoryList.addEventListener('click', function (e) {
                                const categoryItem = e.target.closest('.category-item');
                                if (categoryItem) {
                                    const catId = categoryItem.dataset.category;
                                    loadServices(catId);
                                }
                            });
                        }

                        // Load services function
                        function loadServices(categoryId = 'all') {
                            // If we are just scrolling to a category, do that
                            if (categoryId !== 'all' && document.getElementById(`category-section-${categoryId}`)) {
                                const targetSection = document.getElementById(`category-section-${categoryId}`);
                                const yOffset = -80;
                                const y = targetSection.getBoundingClientRect().top + window.pageYOffset + yOffset;
                                window.scrollTo({ top: y, behavior: 'smooth' });

                                // Update active state manually
                                document.querySelectorAll('.category-item').forEach(c => c.classList.remove('active'));
                                const activeItem = document.querySelector(`.category-item[data-category="${categoryId}"]`);
                                if (activeItem) activeItem.classList.add('active');
                                return;
                            }

                            // Otherwise fetch all (initial load)
                            const servicesSection = document.getElementById('services-section');
                            if (!servicesSection) return;
                            servicesSection.innerHTML = '<div class="spinner"></div>';

                            fetch(`{{ route('booking.guest.services') }}?category=all`, {
                                headers: {
                                    'Accept': 'application/json',
                                }
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    displayServices(data);
                                })
                                .catch(error => {

                                    servicesSection.innerHTML = '<p class="text-red-500 text-center">Error loading services. Please try again.</p>';
                                });
                        }

                        // Service icons mapping
                        const serviceIcons = {
                            'hair': '💇',
                            'nail': '💅',
                            'spa': '🧖',
                            'facial': '✨',
                            'massage': '💆',
                            'makeup': '💄',
                            'default': '✂️'
                        };

                        function getServiceIcon(serviceName) {
                            const name = serviceName.toLowerCase();
                            for (let key in serviceIcons) {
                                if (name.includes(key)) return serviceIcons[key];
                            }
                            return serviceIcons.default;
                        }

                        // Display services
                        function displayServices(services) {
                            allServices = services;
                            const servicesSection = document.getElementById('services-section');
                            if (!servicesSection) return;
                            servicesSection.innerHTML = '';

                            if (services.length === 0) {
                                servicesSection.innerHTML = '<p class="text-gray-500 text-center py-8">No services available.</p>';
                                return;
                            }

                            // Group services by category
                            const groupedServices = {};
                            services.forEach(service => {
                                const catName = service.category ? service.category.name : 'Other Services';
                                const catId = service.category ? service.category.id : 'other';

                                if (!groupedServices[catId]) {
                                    groupedServices[catId] = {
                                        name: catName,
                                        services: []
                                    };
                                }
                                groupedServices[catId].services.push(service);
                            });

                            // Render Categories and Services
                            Object.keys(groupedServices).forEach(catId => {
                                const group = groupedServices[catId];

                                // Category Section Container
                                const sectionDiv = document.createElement('div');
                                sectionDiv.id = `category-section-${catId}`;
                                sectionDiv.className = 'category-section mb-8';

                                // Category Header
                                const header = document.createElement('h3');
                                header.className = 'service-category-header';
                                header.textContent = group.name;
                                sectionDiv.appendChild(header);

                                // Services List
                                group.services.forEach((service, index) => {
                                    const serviceItem = document.createElement('div');
                                    serviceItem.className = 'service-list-item fade-in-up';
                                    serviceItem.style.animationDelay = `${index * 0.05}s`;
                                    serviceItem.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="service-info">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <h4 class="service-name">${service.name}</h4>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="service-meta">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ${service.duration ? `${service.duration} Mins` : ''}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="service-action">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="service-price">{{ $currency_symbol ?? '$' }} ${parseFloat(service.price).toFixed(2)}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         <button class="add-btn" data-service-id="${service.id}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             Add
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                `;
                                    sectionDiv.appendChild(serviceItem);
                                });

                                servicesSection.appendChild(sectionDiv);
                            });

                            // Add event listeners to add buttons
                            document.querySelectorAll('.add-btn').forEach(btn => {
                                btn.addEventListener('click', function () {
                                    const serviceId = this.dataset.serviceId;
                                    const service = allServices.find(s => s.id == serviceId);
                                    if (service) {
                                        addToCart(service);
                                    }
                                });
                            });

                            // Initialize Scroll Spy
                            initScrollSpy();
                        }

                        // Scroll Spy Logic
                        function initScrollSpy() {
                            const sections = document.querySelectorAll('.category-section');
                            const navItems = document.querySelectorAll('.category-item');

                            const observerOptions = {
                                root: null,
                                rootMargin: '-20% 0px -70% 0px',
                                threshold: 0
                            };

                            const observer = new IntersectionObserver((entries) => {
                                entries.forEach(entry => {
                                    if (entry.isIntersecting) {
                                        const id = entry.target.id.replace('category-section-', '');

                                        navItems.forEach(item => item.classList.remove('active'));

                                        const activeItem = document.querySelector(`.category-item[data-category="${id}"]`);
                                        if (activeItem) {
                                            activeItem.classList.add('active');
                                        }
                                    }
                                });
                            }, observerOptions);

                            sections.forEach(section => {
                                observer.observe(section);
                            });
                        }

                        // Add to cart function
                        function addToCart(service) {
                            const existingItem = cart.find(item => item.id === service.id);
                            if (existingItem) {
                                existingItem.quantity += 1;
                            } else {
                                cart.push({ ...service, quantity: 1 });
                            }
                            updateCartDisplay();
                        }

                        // Update cart display
                        function updateCartDisplay() {
                            const cartEmpty = document.getElementById('cart-empty');
                            const cartItems = document.getElementById('cart-items');
                            const cartTotal = document.getElementById('cart-total');

                            if (!cartEmpty || !cartItems || !cartTotal) return;

                            if (cart.length === 0) {
                                cartEmpty.style.display = 'block';
                                cartItems.style.display = 'none';
                                cartTotal.style.display = 'none';
                                return;
                            }

                            cartEmpty.style.display = 'none';
                            cartItems.style.display = 'block';
                            cartTotal.style.display = 'block';

                            let total = 0;
                            let itemsHtml = '';

                            cart.forEach((item, index) => {
                                const itemTotal = item.price * item.quantity;
                                total += itemTotal;
                                itemsHtml += `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div class="cart-item">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="flex justify-between items-start mb-2">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="flex-1">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="cart-item-name">${item.name}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div class="cart-item-price">{{ $currency_symbol ?? '$' }} ${parseFloat(item.price).toFixed(2)} × ${item.quantity}</div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <div class="flex justify-between items-center">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div class="quantity-controls">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <button class="quantity-btn" onclick="changeQuantity(${index}, -1)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="fas fa-minus"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <span class="quantity-value">${item.quantity}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <button class="quantity-btn" onclick="changeQuantity(${index}, 1)">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="fas fa-plus"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <button class="remove-btn" onclick="removeFromCart(${index})">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fas fa-trash"></i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        </button>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            `;
                            });

                            cartItems.innerHTML = itemsHtml;
                            document.getElementById('total-amount').textContent = total.toFixed(2);
                            document.getElementById('hidden-services').value = JSON.stringify(cart);
                        }

                        // Change quantity
                        window.changeQuantity = function (index, delta) {
                            cart[index].quantity += delta;
                            if (cart[index].quantity <= 0) {
                                cart.splice(index, 1);
                            }
                            updateCartDisplay();
                        };

                        // Remove from cart
                        window.removeFromCart = function (index) {
                            cart.splice(index, 1);
                            updateCartDisplay();
                        };

                        // Proceed to booking
                        const proceedBooking = document.getElementById('proceed-booking');
                        if (proceedBooking) {
                            proceedBooking.addEventListener('click', function () {
                                if (cart.length === 0) {
                                    alert('Please add services to your cart first.');
                                    return;
                                }

                                // Populate selected services summary
                                const summaryDiv = document.getElementById('selected-services-summary');
                                if (summaryDiv) {
                                    summaryDiv.innerHTML = '';
                                    cart.forEach(item => {
                                        const serviceDiv = document.createElement('div');
                                        serviceDiv.className = 'service-summary-item';
                                        serviceDiv.innerHTML = `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="service-summary-name">${item.name} (×${item.quantity})</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="service-summary-price">{{ $currency_symbol ?? '$' }} ${(item.price * item.quantity).toFixed(2)}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                `;
                                        summaryDiv.appendChild(serviceDiv);
                                    });
                                }

                                // Show modal
                                // document.getElementById('booking-modal').style.display = 'flex';

                                // Inline Flow Logic
                                if (currentStep === 1) {
                                    showBookingOptions();
                                } else {
                                    // Validate selection
                                    if (!selectedDate) {
                                        alert('Please select a date.');
                                        return;
                                    }
                                    if (!selectedTime) {
                                        alert('Please select a time.');
                                        return;
                                    }

                                    // Show personal info modal (simplified)
                                    document.getElementById('booking-modal').style.display = 'flex';
                                }
                            });
                        }



                        function showBookingOptions() {
                            currentStep = 2;
                            document.getElementById('services-section').style.display = 'none';
                            document.getElementById('category-sidebar-container').style.display = 'none';

                            const bookingSection = document.getElementById('booking-options-section');
                            if (bookingSection) {
                                bookingSection.style.display = 'block';
                                bookingSection.classList.remove('lg:col-span-6');
                                bookingSection.classList.add('lg:col-span-9');
                            }

                            // Filter Staff based on Cart
                            filterStaffByServices();

                            // Update Proceed Button
                            const proceedBtn = document.getElementById('proceed-booking');
                            if (proceedBtn) {
                                proceedBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Book Now';
                            }

                            // Render Dates if not already done
                            if (document.getElementById('date-selection-container').children.length === 0) {
                                renderDates();
                            }

                            // Add "Back" button to cart header if not exists
                            const cartHeader = document.querySelector('.cart-header');
                            if (!document.getElementById('back-to-services')) {
                                const backBtn = document.createElement('button');
                                backBtn.id = 'back-to-services';
                                backBtn.className = 'text-sm text-gray-500 hover:text-gray-700 float-right font-normal';
                                backBtn.innerHTML = '<i class="fas fa-arrow-left mr-1"></i>Back';
                                backBtn.onclick = showServices;
                                cartHeader.appendChild(backBtn);
                            } else {
                                document.getElementById('back-to-services').style.display = 'inline-block';
                            }
                        }

                        function filterStaffByServices() {
                            const staffOptions = document.querySelectorAll('.staff-option');

                            // If cart is empty, show all (shouldn't happen)
                            if (cart.length === 0) {
                                staffOptions.forEach(opt => opt.style.display = 'flex');
                                return;
                            }

                            // Get all staff IDs from all services in cart
                            // We need to find staff who can perform ALL services (intersection)
                            // Or at least, filter out staff who can't perform ANY of the selected services?
                            // Usually for a single booking flow, we want a staff who can do the sequence.
                            // But let's start with: Staff must be able to perform ALL selected services.

                            // First service's staff
                            let eligibleStaffIds = cart[0].staff.map(s => s.id);

                            // Intersect with subsequent services
                            for (let i = 1; i < cart.length; i++) {
                                const serviceStaffIds = cart[i].staff.map(s => s.id);
                                eligibleStaffIds = eligibleStaffIds.filter(id => serviceStaffIds.includes(id));
                            }

                            staffOptions.forEach(opt => {
                                const staffId = parseInt(opt.dataset.staffId);
                                if (eligibleStaffIds.includes(staffId)) {
                                    opt.style.display = 'flex';
                                } else {
                                    opt.style.display = 'none';
                                }
                            });

                            // Reset selection to "Any Staff" if current selection is hidden
                            const currentSelection = document.querySelector('input[name="staff_selection"]:checked');
                            if (currentSelection && currentSelection.value !== '') {
                                const currentOption = currentSelection.closest('.staff-option');
                                if (currentOption && currentOption.style.display === 'none') {
                                    document.querySelector('input[name="staff_selection"][value=""]').checked = true;
                                    selectedStaff = '';
                                    fetchInlineSlots();
                                }
                            }
                        }

                        function showServices() {
                            currentStep = 1;
                            document.getElementById('services-section').style.display = 'grid'; // Restore grid layout
                            document.getElementById('category-sidebar-container').style.display = 'block';

                            const bookingSection = document.getElementById('booking-options-section');
                            if (bookingSection) {
                                bookingSection.style.display = 'none';
                                bookingSection.classList.remove('lg:col-span-9');
                                bookingSection.classList.add('lg:col-span-6');
                            }

                            // Update Proceed Button
                            const proceedBtn = document.getElementById('proceed-booking');
                            if (proceedBtn) {
                                proceedBtn.innerHTML = '<i class="fas fa-calendar-check mr-2"></i>Select Staff, Date & Time';
                            }

                            // Hide Back Button
                            const backBtn = document.getElementById('back-to-services');
                            if (backBtn) backBtn.style.display = 'none';
                        }

                        function renderDates() {
                            const container = document.getElementById('date-selection-container');
                            if (!container) return;

                            const advanceDays = parseInt('{{ $advance_booking_days ?? 30 }}');
                            const today = new Date();
                            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                            const batchSize = 14;
                            const remainingDays = advanceDays - (datesRendered || 0);
                            const currentBatch = Math.min(batchSize, remainingDays);

                            if (currentBatch <= 0) {
                                const showMoreBtn = document.getElementById('show-more-dates');
                                if (showMoreBtn) showMoreBtn.style.display = 'none';
                                return;
                            }

                            for (let i = 0; i < currentBatch; i++) {
                                const date = new Date(today);
                                date.setDate(today.getDate() + (datesRendered || 0) + i);

                                const dateString = date.toISOString().split('T')[0];
                                const dayName = days[date.getDay()];
                                const dayNum = date.getDate();
                                const monthName = months[date.getMonth()];

                                const card = document.createElement('div');
                                card.className = 'date-card';
                                card.dataset.date = dateString;
                                card.innerHTML = `
                                                                                                                                                            <div class="date-day-name text-xs text-gray-500 uppercase mb-1">${dayName}</div>
                                                                                                                                                            <div class="date-day">${String(dayNum).padStart(2, '0')}</div>
                                                                                                                                                            <div class="date-month">${monthName}</div>
                                                                                                                                                        `;

                                card.addEventListener('click', function () {
                                    document.querySelectorAll('.date-card').forEach(c => c.classList.remove('active'));
                                    this.classList.add('active');
                                    selectedDate = this.dataset.date;
                                    document.getElementById('hidden-date').value = selectedDate;
                                    fetchInlineSlots();
                                });

                                container.appendChild(card);
                            }
                            datesRendered += currentBatch;

                            if (datesRendered >= advanceDays) {
                                const showMoreBtn = document.getElementById('show-more-dates');
                                if (showMoreBtn) showMoreBtn.style.display = 'none';
                            }
                        }

                        // Staff Selection Listener
                        document.querySelectorAll('input[name="staff_selection"]').forEach(radio => {
                            radio.addEventListener('change', function () {
                                selectedStaff = this.value;
                                document.getElementById('hidden-staff').value = selectedStaff;
                                if (selectedDate) {
                                    fetchInlineSlots();
                                }
                            });
                        });

                        function fetchInlineSlots() {
                            if (!selectedDate) return;

                            const timeContainer = document.getElementById('time-selection-container');
                            if (timeContainer) {
                                timeContainer.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-2xl text-teal-500"></i></div>';
                            }

                            // Prepare params
                            const params = new URLSearchParams();
                            params.append('date', selectedDate);
                            if (selectedStaff) params.append('staff_id', selectedStaff);

                            cart.forEach((service, index) => {
                                params.append(`services[${index}][id]`, service.id);
                                params.append(`services[${index}][quantity]`, service.quantity);
                            });

                            fetch(`{{ route('booking.guest.slots') }}?${params.toString()}`, {
                                headers: { 'Accept': 'application/json' }
                            })
                                .then(res => res.json())
                                .then(data => {
                                    renderTimeSlots(data.slots || []);
                                })
                                .catch(err => {

                                    if (timeContainer) {
                                        timeContainer.innerHTML = '<div class="text-center text-red-500 py-4">Error loading slots</div>';
                                    }
                                });
                        }

                        function renderTimeSlots(slots) {
                            const container = document.getElementById('time-selection-container');
                            if (!container) return;
                            container.innerHTML = '';

                            if (slots.length === 0) {
                                container.innerHTML = '<div class="text-center text-gray-500 py-4">No available slots for this date</div>';
                                return;
                            }

                            const earlySlots = slots.filter(time => parseInt(time.split(':')[0]) < 12);
                            const lateSlots = slots.filter(time => parseInt(time.split(':')[0]) >= 12);

                            let html = '';

                            if (earlySlots.length > 0) {
                                html += '<h5 class="time-section-label">Early Hours</h5><div class="time-grid">';
                                earlySlots.forEach(time => {
                                    html += createTimeButton(time);
                                });
                                html += '</div>';
                            }

                            if (lateSlots.length > 0) {
                                html += '<h5 class="time-section-label">Late Hours</h5><div class="time-grid">';
                                lateSlots.forEach(time => {
                                    html += createTimeButton(time);
                                });
                                html += '</div>';
                            }

                            container.innerHTML = html;

                            // Re-attach listeners
                            container.querySelectorAll('.time-slot-btn').forEach(btn => {
                                btn.addEventListener('click', function () {
                                    document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('active'));
                                    this.classList.add('active');
                                    selectedTime = this.dataset.time;
                                    document.getElementById('hidden-time').value = selectedTime;
                                });
                            });
                        }

                        function createTimeButton(time) {
                            // Format time (HH:mm:ss -> hh:mm AM/PM)
                            const [hours, minutes] = time.split(':');
                            const date = new Date();
                            date.setHours(hours);
                            date.setMinutes(minutes);
                            const formatted = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                            return `<button class="time-slot-btn" data-time="${time}">${formatted}</button>`;
                        }

                        // Close success modal
                        const closeSuccessBtn = document.getElementById('close-success');
                        if (closeSuccessBtn) {
                            closeSuccessBtn.addEventListener('click', function () {
                                document.getElementById('success-modal').style.display = 'none';
                                window.location.reload();
                            });
                        }

                        const showMoreDatesBtn = document.getElementById('show-more-dates');
                        if (showMoreDatesBtn) {
                            showMoreDatesBtn.addEventListener('click', function () {
                                renderDates();
                            });
                        }

                        const closeModalBtn = document.getElementById('close-modal');
                        if (closeModalBtn) {
                            closeModalBtn.addEventListener('click', function () {
                                document.getElementById('booking-modal').style.display = 'none';
                            });
                        }

                        const cancelBookingBtn = document.getElementById('cancel-booking');
                        if (cancelBookingBtn) {
                            cancelBookingBtn.addEventListener('click', function () {
                                document.getElementById('booking-modal').style.display = 'none';
                            });
                        }


                        // Real-time validation for booking form
                        const phoneField = document.getElementById('phone');
                        const emailField = document.getElementById('email');

                        if (phoneField) {
                            phoneField.addEventListener('blur', function () {
                                const phone = this.value.trim();
                                clearError('phone');
                                if (phone && window.customerPhoneHandler && !window.customerPhoneHandler.validatePhoneNumber(window.itiPhone, this, false)) {
                                    showError('phone', 'Please enter a valid phone number.');
                                }
                            });
                        }

                        if (emailField) {
                            emailField.addEventListener('blur', function () {
                                const email = this.value.trim();
                                clearError('email');
                                if (email && !validateEmail(email)) {
                                    showError('email', 'Please enter a valid email address.');
                                }
                            });
                        }





                        // Close modal on outside click
                        const bookingModal = document.getElementById('booking-modal');
                        if (bookingModal) {
                            bookingModal.addEventListener('click', function (e) {
                                if (e.target === this) {
                                    this.style.display = 'none';
                                }
                            });
                        }

                        const successModal = document.getElementById('success-modal');
                        if (successModal) {
                            successModal.addEventListener('click', function (e) {
                                if (e.target === this) {
                                    this.style.display = 'none';
                                    window.location.reload();
                                }
                            });
                        }
                    } catch (error) {

                    }
                });
            </script>
        @endpush

    @endif

@endsection