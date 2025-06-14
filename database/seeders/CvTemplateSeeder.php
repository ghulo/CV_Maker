<?php

namespace Database\Seeders;

use App\Models\CvTemplate;
use Illuminate\Database\Seeder;

class CvTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Modern Professional',
                'slug' => 'modern-professional',
                'description' => 'A clean and modern template perfect for professionals.',
                'style_config' => json_encode([
                    'fontFamily' => 'Inter, sans-serif',
                    'lineHeight' => '1.6',
                    'spacing' => 'normal',
                ]),
                'color_schemes' => json_encode([
                    [
                        'name' => 'Classic Blue',
                        'colors' => [
                            'primary' => '#1a56db',
                            'secondary' => '#4b5563',
                            'accent' => '#e5edff',
                            'text' => '#111827',
                        ],
                    ],
                    [
                        'name' => 'Professional Gray',
                        'colors' => [
                            'primary' => '#374151',
                            'secondary' => '#6b7280',
                            'accent' => '#f3f4f6',
                            'text' => '#111827',
                        ],
                    ],
                ]),
                'is_premium' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Creative Portfolio',
                'slug' => 'creative-portfolio',
                'description' => 'A creative template designed for artists and designers.',
                'style_config' => json_encode([
                    'fontFamily' => 'Poppins, sans-serif',
                    'lineHeight' => '1.7',
                    'spacing' => 'relaxed',
                ]),
                'color_schemes' => json_encode([
                    [
                        'name' => 'Vibrant',
                        'colors' => [
                            'primary' => '#7c3aed',
                            'secondary' => '#9333ea',
                            'accent' => '#f5f3ff',
                            'text' => '#111827',
                        ],
                    ],
                    [
                        'name' => 'Minimal',
                        'colors' => [
                            'primary' => '#000000',
                            'secondary' => '#4b5563',
                            'accent' => '#f9fafb',
                            'text' => '#111827',
                        ],
                    ],
                ]),
                'is_premium' => true,
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            CvTemplate::create($template);
        }
    }
} 