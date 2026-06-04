<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessagesSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1 (555) 123-4567',
                'subject' => 'Catering Inquiry',
                'message' => 'Hello, I am planning a corporate event for 50 people next month. Could you please send me information about your catering services and menu options?',
                'message_type' => 'general',
                'status' => 'replied',
                'reply_message' => 'Thank you for your inquiry. We have sent our catering brochure to your email. Our event coordinator will contact you within 24 hours.',
                'replied_at' => now()->subDays(1),
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@example.com',
                'phone' => '+1 (555) 987-6543',
                'subject' => 'Allergy Information',
                'message' => 'I have severe nut allergies. Do you have a separate preparation area for nut-free dishes? Also, could you share your allergen menu?',
                'message_type' => 'feedback',
                'status' => 'replied',
                'reply_message' => 'Thank you for your question. Yes, we have a dedicated allergen-free preparation area. Our complete allergen menu is available on our website.',
                'replied_at' => now()->subHours(12),
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'phone' => '+1 (555) 456-7890',
                'subject' => 'Compliment - Excellent Service',
                'message' => 'I dined at your restaurant last night and wanted to compliment your waiter James. He provided exceptional service.',
                'message_type' => 'feedback',
                'status' => 'replied',
                'reply_message' => 'Thank you for your kind words! We are delighted to hear about your positive experience.',
                'replied_at' => now()->subHours(3),
            ],
            [
                'name' => 'Emily Chen',
                'email' => 'emily.chen@example.com',
                'phone' => '+1 (555) 789-0123',
                'subject' => 'Private Dining Room Booking',
                'message' => 'I would like to book your private dining room for a birthday party on December 15th for 20 people. What are the rates and availability?',
                'message_type' => 'reservation',
                'status' => 'unread',
            ],
            [
                'name' => 'Robert Taylor',
                'email' => 'robert.taylor@example.com',
                'phone' => '+1 (555) 234-5678',
                'subject' => 'Recipe Inquiry',
                'message' => 'I absolutely loved the spicy garlic sauce with my noodles last week. Is there any chance you could share the recipe?',
                'message_type' => 'suggestion',
                'status' => 'replied',
                'reply_message' => 'Thank you for your interest in our sauce! Unfortunately, our recipes are proprietary.',
                'replied_at' => now()->subDays(2),
            ],
        ];

        foreach ($messages as $message) {
            ContactMessage::create($message);
        }
    }
}
