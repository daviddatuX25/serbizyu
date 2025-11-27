#!/usr/bin/env python3
"""
Laravel Service Seeder Generator from Sorted Image Folders

This script converts a folder structure of sorted images into a Laravel 
seeder that creates Service records with categories and attaches images.

Usage:
    python image-to-seeder.py --source "C:/path/to/images" --seeder MyServicesSeeder --price 100
"""

import os
import sys
import argparse
from pathlib import Path
from typing import List, Dict

def get_image_categories(source_path: str) -> Dict[str, List[str]]:
    """
    Scan source path and organize images by category (folder).
    
    Returns: dict like {'category_name': ['image1.jpg', 'image2.png']}
    """
    source = Path(source_path)
    
    if not source.exists():
        print(f"❌ Source path not found: {source_path}")
        sys.exit(1)
    
    categories = {}
    image_extensions = {'.jpg', '.jpeg', '.png', '.gif', '.webp'}
    
    # Get all subdirectories
    for category_folder in sorted(source.iterdir()):
        if not category_folder.is_dir() or category_folder.name.startswith('.'):
            continue
        
        # Get images in this category
        images = [
            f.name for f in category_folder.iterdir() 
            if f.is_file() and f.suffix.lower() in image_extensions
        ]
        
        if images:
            categories[category_folder.name] = sorted(images)
    
    return categories

def title_case(text: str) -> str:
    """Convert underscore/hyphen separated text to Title Case."""
    return ' '.join(word.capitalize() for word in text.replace('_', ' ').replace('-', ' ').split())

def generate_seeder(
    categories: Dict[str, List[str]], 
    seeder_name: str = "ImageServicesSeeder",
    default_price: float = 0
) -> str:
    """
    Generate PHP seeder code.
    """
    
    php_code = f"""<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use App\\Domains\\Listings\\Models\\Service;
use App\\Domains\\Listings\\Models\\Category;
use App\\Domains\\Users\\Models\\User;
use App\\Enums\\PaymentMethod;
use Illuminate\\Support\\Facades\\Storage;

class {seeder_name} extends Seeder
{{
    /**
     * Run the database seeds.
     * 
     * This seeder creates Service records from images organized in category folders.
     * Images should be in: storage/app/public/images/{{category_name}}/{{image_file}}
     */
    public function run(): void
    {{
        // Get or create the default creator (usually admin user)
        $creator = User::where('role', 'creator')->orWhere('is_admin', true)->first();
        
        if (!$creator) {{
            $this->command->error('No creator user found. Please create a user first.');
            return;
        }}

        // Services to create with their images
        $servicesData = [
"""
    
    # Add services
    for category, images in categories.items():
        for image in images:
            image_name = Path(image).stem
            title = title_case(image_name)
            image_relative_path = f"images/{category}/{image}"
            
            php_code += f"""            [
                'category' => '{category}',
                'title' => '{title}',
                'description' => 'Professional {category} service - {image_name}',
                'price' => {default_price},
                'image_path' => '{image_relative_path}',
            ],
"""
    
    php_code += """        ];

        foreach ($servicesData as $data) {
            // Get or create category
            $category = Category::firstOrCreate(
                ['name' => $data['category']],
                ['slug' => strtolower(str_replace(' ', '-', $data['category']))]
            );

            // Create service
            $service = Service::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'price' => $data['price'] ?? 0,
                'pay_first' => true,
                'payment_method' => PaymentMethod::XENDIT,
                'category_id' => $category->id,
                'creator_id' => $creator->id,
            ]);

            // Attach image if exists
            if ($data['image_path'] && Storage::disk('public')->exists($data['image_path'])) {
                $fullPath = Storage::disk('public')->path($data['image_path']);
                $service->attachMedia($fullPath, ['gallery'], ['description' => $data['title']]);
            }

            $this->command->info("✅ Created: {$service->title}");
        }

        $this->command->info("\\n✨ Service seeder completed!");
    }
}
"""
    
    return php_code

def main():
    parser = argparse.ArgumentParser(
        description="Generate Laravel seeders from sorted image folders",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  python image-to-seeder.py --source "C:/Images" --seeder MyServicesSeeder --price 100
  python image-to-seeder.py --source "./images" --output "./database/seeders"
        """
    )
    
    parser.add_argument(
        '--source', '-s',
        required=True,
        help='Path to folder containing sorted image folders by category'
    )
    
    parser.add_argument(
        '--seeder', '-n',
        default='ImageServicesSeeder',
        help='Name of the seeder class (default: ImageServicesSeeder)'
    )
    
    parser.add_argument(
        '--price', '-p',
        type=float,
        default=0,
        help='Default price for all services (default: 0)'
    )
    
    parser.add_argument(
        '--output', '-o',
        help='Output directory for seeder (default: ./database/seeders)'
    )
    
    args = parser.parse_args()
    
    # Get categories and images
    categories = get_image_categories(args.source)
    
    if not categories:
        print(f"❌ No image folders found in: {args.source}")
        sys.exit(1)
    
    print(f"✅ Found {len(categories)} categories:")
    for cat, images in categories.items():
        print(f"   📁 {cat} - {len(images)} image(s)")
    
    # Generate seeder
    seeder_code = generate_seeder(categories, args.seeder, args.price)
    
    # Determine output path
    if args.output:
        output_dir = Path(args.output)
    else:
        output_dir = Path('./database/seeders')
    
    output_dir.mkdir(parents=True, exist_ok=True)
    seeder_file = output_dir / f"{args.seeder}.php"
    
    # Write file
    seeder_file.write_text(seeder_code, encoding='utf-8')
    
    print(f"\n✅ Seeder created: {seeder_file}")
    print(f"\n📋 Next steps:")
    print(f"   1. Copy your image folders to: storage/app/public/images/")
    print(f"   2. Run: php artisan storage:link")
    print(f"   3. Run: php artisan db:seed --class={args.seeder}")
    print(f"\n💡 The seeder will:")
    print(f"   • Create Category records from folder names")
    print(f"   • Create Service records with proper metadata")
    print(f"   • Attach images using Mediable")

if __name__ == '__main__':
    main()
