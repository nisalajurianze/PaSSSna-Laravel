<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewsSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first();

        $reviews = [
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 5,
                'comment' => 'Amazing food and excellent service! The grilled salmon was perfectly cooked, and the staff were incredibly attentive. Will definitely come back!',
                'is_approved' => true,
                'helpful_count' => 12,
                'reviewer_name' => 'John Smith',
                'review_type' => 'overall',
            ],
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 4,
                'comment' => 'Great atmosphere and delicious food. The chocolate lava cake is to die for! Only suggestion would be to have more vegetarian options.',
                'is_approved' => true,
                'helpful_count' => 8,
                'reviewer_name' => 'Sarah Johnson',
                'review_type' => 'overall',
            ],
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 5,
                'comment' => 'Best restaurant in town! The PaSSSna Special Platter is absolutely worth trying. Service was fast and professional.',
                'is_approved' => true,
                'helpful_count' => 15,
                'reviewer_name' => 'Michael Chen',
                'review_type' => 'overall',
            ],
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 3,
                'comment' => 'Food was good but the wait time was longer than expected. The staff apologized and offered free dessert though, which was nice.',
                'is_approved' => true,
                'helpful_count' => 5,
                'reviewer_name' => 'Emma Wilson',
                'review_type' => 'overall',
            ],
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 5,
                'comment' => 'Perfect for special occasions! We celebrated our anniversary here and the staff made it extra special with complimentary champagne.',
                'is_approved' => true,
                'helpful_count' => 20,
                'reviewer_name' => 'David Brown',
                'review_type' => 'overall',
            ],
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 4,
                'comment' => 'Loved the custom meal option! Being able to create my own dish with fresh ingredients was fantastic. Highly recommended for picky eaters.',
                'is_approved' => true,
                'helpful_count' => 7,
                'reviewer_name' => 'Lisa Martinez',
                'review_type' => 'food',
            ],
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 5,
                'comment' => 'Exceptional dining experience from start to finish. The reservation process was smooth, food arrived hot, and presentation was beautiful.',
                'is_approved' => true,
                'helpful_count' => 11,
                'reviewer_name' => 'Robert Taylor',
                'review_type' => 'service',
            ],
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 4,
                'comment' => 'Great value for money with the weekend promotions. Family enjoyed the meal and the kids loved the dessert selection.',
                'is_approved' => true,
                'helpful_count' => 6,
                'reviewer_name' => 'Jennifer Lee',
                'review_type' => 'overall',
            ],
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 5,
                'comment' => 'As a food critic, I must say this restaurant exceeded expectations. The attention to detail in both food and service is remarkable.',
                'is_approved' => true,
                'helpful_count' => 25,
                'reviewer_name' => 'Thomas Anderson',
                'review_type' => 'overall',
            ],
            [
                'user_id' => $adminUser ? $adminUser->id : 1,
                'rating' => 3,
                'comment' => 'Good food but the noise level was quite high. Would suggest adding more sound-absorbing materials in the dining area.',
                'is_approved' => true,
                'helpful_count' => 3,
                'reviewer_name' => 'Olivia Garcia',
                'review_type' => 'ambiance',
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
