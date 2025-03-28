# Dental Price Comparison System

This is a Laravel-based application that helps users find the best supplier for dental products based on price and quantity. The system takes product requests, compares prices from multiple suppliers, and returns the most cost-effective option.

## Features
- Suppliers can have multiple product listings with different sizes and prices.
- Users can request bulk quantities, and the system selects the best supplier.
- Efficient price calculation to minimize total cost.
- REST API endpoint for easy integration.

## Installation
Clone the repository:
   git clone https://github.com/your-username/dental-price-comparison.git
   cd dental-price-comparison

   Install dependencies:
   composer install

   Set up the database:
   php artisan migrate --seed

   Start the application:
   php artisan serve
