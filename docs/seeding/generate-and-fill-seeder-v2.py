#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Enhanced Seeder Generation Pipeline v2
- Cleans up category names (remove underscores)
- Groups multiple images per listing (2-3 images per service/offer)
- Maintains 70/30 split
- Adds duration_minutes to workflow steps
- Adds reviews for each listing
"""

import os
import json
import sys
import random
from pathlib import Path
from datetime import datetime

# Force UTF-8 output encoding
if sys.stdout.encoding != 'utf-8':
    sys.stdout.reconfigure(encoding='utf-8')

# Get script directory for relative path resolution
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))

def get_image_extensions():
    """Supported image extensions"""
    return {'.jpg', '.jpeg', '.png', '.gif', '.webp', '.heic'}

def clean_category_name(category):
    """Convert underscore category names to readable format"""
    # Replace underscores with spaces
    cleaned = category.replace('_', ' ')
    # Lowercase everything first, then capitalize first letter of non-small words
    words = cleaned.split()
    result = []
    for word in words:
        if word.lower() in ['and', 'the', 'a', 'of', '&']:
            result.append(word.lower())
        else:
            result.append(word.capitalize())
    # Capitalize first word regardless
    if result:
        result[0] = result[0].capitalize()
    return ' '.join(result)

def scan_listing_seeder(base_path=None):
    """Scan listing_seeder directory and return all images grouped by category"""
    if base_path is None:
        base_path = os.path.join(SCRIPT_DIR, "listing_seeder")
        if not os.path.exists(base_path):
            root_dir = os.path.dirname(os.path.dirname(SCRIPT_DIR))
            base_path = os.path.join(root_dir, "listing_seeder")
    elif not os.path.isabs(base_path):
        base_path = os.path.join(SCRIPT_DIR, base_path)
    
    categories_data = {}
    
    if not os.path.exists(base_path):
        print(f"Error: '{base_path}' directory not found")
        return None
    
    print(f"Scanning {base_path} directory...")
    
    # Iterate through category folders
    for category_name in sorted(os.listdir(base_path)):
        category_path = os.path.join(base_path, category_name)
        
        if not os.path.isdir(category_path):
            continue
        
        # Keep original name for directory lookup but also store clean name
        clean_name = clean_category_name(category_name)
        print(f"  Found category: {clean_name}")
        
        images_list = []
        
        # Scan all files in category directory
        for filename in sorted(os.listdir(category_path)):
            filepath = os.path.join(category_path, filename)
            
            if os.path.isfile(filepath):
                ext = os.path.splitext(filename)[1].lower()
                if ext in get_image_extensions():
                    images_list.append({
                        'filename': filename,
                        'relative_path': f"{category_name}/{filename}",  # Keep original dir name
                    })
        
        if images_list:
            categories_data[clean_name] = images_list
    
    return categories_data

def get_workflow_definitions():
    """Define workflows and their steps with duration"""
    return {
        "Basic Plumbing Service": [
            {"name": "Initial Consultation", "work_catalog_ref": "Initial Consultation", "duration_minutes": 30},
            {"name": "Diagnostic Assessment", "work_catalog_ref": "Site Inspection", "duration_minutes": 45},
            {"name": "Repair Execution", "work_catalog_ref": "Work Execution", "duration_minutes": 120},
            {"name": "Final Inspection", "work_catalog_ref": "Quality Check", "duration_minutes": 30}
        ],
        "House Painting Service": [
            {"name": "Design Consultation", "work_catalog_ref": "Initial Consultation", "duration_minutes": 30},
            {"name": "Surface Preparation", "work_catalog_ref": "Material/Resource Preparation", "duration_minutes": 240},
            {"name": "Painting Execution", "work_catalog_ref": "Work Execution", "duration_minutes": 480},
            {"name": "Quality Inspection", "work_catalog_ref": "Quality Check", "duration_minutes": 45}
        ],
        "Event Catering Service": [
            {"name": "Menu Planning", "work_catalog_ref": "Initial Consultation", "duration_minutes": 60},
            {"name": "Ingredient Sourcing", "work_catalog_ref": "Material/Resource Preparation", "duration_minutes": 120},
            {"name": "Food Preparation", "work_catalog_ref": "Work Execution", "duration_minutes": 360},
            {"name": "Service & Setup", "work_catalog_ref": "Client Approval", "duration_minutes": 120}
        ],
        "Event Decoration Service": [
            {"name": "Venue Assessment", "work_catalog_ref": "Initial Consultation", "duration_minutes": 45},
            {"name": "Design Planning", "work_catalog_ref": "Site Inspection", "duration_minutes": 90},
            {"name": "Decoration Setup", "work_catalog_ref": "Work Execution", "duration_minutes": 300},
            {"name": "Final Walkthrough", "work_catalog_ref": "Quality Check", "duration_minutes": 30}
        ],
        "Electrical Repair Service": [
            {"name": "Initial Consultation", "work_catalog_ref": "Initial Consultation", "duration_minutes": 30},
            {"name": "Electrical Inspection", "work_catalog_ref": "Site Inspection", "duration_minutes": 60},
            {"name": "Repair Work", "work_catalog_ref": "Work Execution", "duration_minutes": 180},
            {"name": "Safety Testing", "work_catalog_ref": "Quality Check", "duration_minutes": 45}
        ],
        "Small Construction Project": [
            {"name": "Project Consultation", "work_catalog_ref": "Initial Consultation", "duration_minutes": 60},
            {"name": "Site Inspection", "work_catalog_ref": "Site Inspection", "duration_minutes": 90},
            {"name": "Material Preparation", "work_catalog_ref": "Material/Resource Preparation", "duration_minutes": 180},
            {"name": "Construction Work", "work_catalog_ref": "Work Execution", "duration_minutes": 480},
            {"name": "Quality Inspection", "work_catalog_ref": "Quality Check", "duration_minutes": 120},
            {"name": "Client Walkthrough", "work_catalog_ref": "Client Approval", "duration_minutes": 60}
        ],
        "AC Repair Service": [
            {"name": "Initial Assessment", "work_catalog_ref": "Initial Consultation", "duration_minutes": 30},
            {"name": "System Inspection", "work_catalog_ref": "Site Inspection", "duration_minutes": 45},
            {"name": "Repair/Installation", "work_catalog_ref": "Work Execution", "duration_minutes": 150},
            {"name": "System Testing", "work_catalog_ref": "Quality Check", "duration_minutes": 30}
        ],
        "Car Maintenance Service": [
            {"name": "Vehicle Assessment", "work_catalog_ref": "Initial Consultation", "duration_minutes": 30},
            {"name": "Diagnostic Check", "work_catalog_ref": "Site Inspection", "duration_minutes": 60},
            {"name": "Maintenance Work", "work_catalog_ref": "Work Execution", "duration_minutes": 120},
            {"name": "Quality Inspection", "work_catalog_ref": "Quality Check", "duration_minutes": 30}
        ],
        "Website Design Service": [
            {"name": "Consultation & Planning", "work_catalog_ref": "Initial Consultation", "duration_minutes": 60},
            {"name": "Design Preparation", "work_catalog_ref": "Material/Resource Preparation", "duration_minutes": 300},
            {"name": "Development Work", "work_catalog_ref": "Work Execution", "duration_minutes": 720},
            {"name": "Testing & Review", "work_catalog_ref": "Quality Check", "duration_minutes": 120}
        ],
        "Cleaning Service": [
            {"name": "Initial Assessment", "work_catalog_ref": "Initial Consultation", "duration_minutes": 20},
            {"name": "Cleaning Execution", "work_catalog_ref": "Work Execution", "duration_minutes": 180},
            {"name": "Quality Verification", "work_catalog_ref": "Quality Check", "duration_minutes": 20}
        ]
    }

def get_listing_definitions():
    """Define listings with their metadata"""
    return [
        # Agriculture (2)
        ("Agriculture and Landscaping", "Professional Crop Planting", "Expert crop planting with soil prep and seed selection", 450, "Small Construction Project", 2),
        ("Agriculture and Landscaping", "Tree Removal Service", "Professional arborist tree removal with cleanup", 650, "Small Construction Project", 2),
        
        # Care & Cleaning (2)
        ("Care and Cleaning Services", "Childcare Services", "Professional nanny with childcare expertise", 350, "Cleaning Service", 2),
        ("Care and Cleaning Services", "Janitorial Cleaning", "Comprehensive cleaning for offices and homes", 280, "Cleaning Service", 2),
        
        # Community (1)
        ("Community and Social Services", "Environmental Cleanup", "Community cleanup and waste removal", 320, "Cleaning Service", 2),
        
        # Construction & Engineering (2)
        ("Construction & Engineering", "Plumbing Consultation", "Expert plumbing design and planning", 400, "Basic Plumbing Service", 2),
        ("Construction & Engineering", "Steelwork Installation", "Professional steel structure installation", 1200, "Small Construction Project", 3),
        
        # Property Maintenance (2)
        ("Construction and Property Maintenance", "House Painting", "Interior and exterior painting service", 550, "House Painting Service", 2),
        ("Construction and Property Maintenance", "Masonry Work", "Professional masonry and cement services", 680, "Small Construction Project", 2),
        
        # Skilled Trades (7)
        ("Construction and Skilled Trades", "Plumbing Repair", "Complete plumbing repair and maintenance", 420, "Basic Plumbing Service", 2),
        ("Construction And Skilled Trades", "Carpentry Services", "Custom carpentry and framing", 520, "Small Construction Project", 2),
        ("Construction And Skilled Trades", "Workshop Carpentry", "Custom woodworking in our facility", 480, "Small Construction Project", 2),
        ("Construction And Skilled Trades", "Construction Management", "Project planning and supervision", 800, "Small Construction Project", 2),
        ("Construction And Skilled Trades", "Electrical Maintenance", "System repair and installation", 380, "Electrical Repair Service", 2),
        ("Construction And Skilled Trades", "Plumbing Repairs", "Advanced plumbing solutions", 450, "Basic Plumbing Service", 2),
        ("Construction And Skilled Trades", "Welding Services", "Metal fabrication and welding", 550, "Small Construction Project", 2),
        
        # Education (1)
        ("Education And Training", "Training Programs", "Technical education and training", 300, "Website Design Service", 2),
        
        # Facility Maintenance (5)
        ("Facility And Grounds Maintenance", "Floor Care", "Professional floor maintenance", 260, "Cleaning Service", 2),
        ("Facility And Grounds Maintenance", "Landscaping Service", "Tree and grounds maintenance", 420, "Small Construction Project", 2),
        ("Facility And Grounds Maintenance", "Window Cleaning", "Professional window cleaning", 200, "Cleaning Service", 2),
        ("Facility And Grounds Maintenance", "Tree Removal", "Safe removal and land clearing", 600, "Small Construction Project", 2),
        ("Facility And Grounds Maintenance", "Grounds Maintenance Wanted", "Seeking professional grounds maintenance", 2000, "Small Construction Project", 2),
        
        # Food & Agriculture (2)
        ("Food & Agriculture", "Aquaculture Services", "Fish farming and harvesting", 800, "Small Construction Project", 2),
        ("Food & Agriculture", "Culinary Training", "Professional cooking education", 350, "Event Catering Service", 2),
        
        # Graphic Design (2)
        ("Graphic Design", "Digital Media Services", "Graphics and illustration design", 500, "Website Design Service", 2),
        ("Graphic Design", "Creative Arts Design", "Logo and branding services", 450, "Website Design Service", 2),
        
        # Health & Wellness (4)
        ("Health and Wellness", "Fitness Coaching", "Personal fitness training", 300, "AC Repair Service", 2),
        ("Health and Wellness", "Personal Training", "Customized fitness programs", 320, "AC Repair Service", 2),
        ("Health and Wellness", "Spa Services", "Massage and wellness therapy", 280, "AC Repair Service", 2),
        ("Health and Wellness", "Balinese Massage", "Traditional massage therapy", 250, "AC Repair Service", 2),
        
        # Hospitality Culinary (3)
        ("Hospitality and Culinary Arts", "Chef Services", "Professional culinary services", 650, "Event Catering Service", 2),
        ("Hospitality and Culinary Arts", "Kitchen Team", "Catering team and support", 550, "Event Catering Service", 2),
        ("Hospitality and Culinary Arts", "Hospitality Services", "Professional hospitality coordination", 400, "Event Decoration Service", 2),
        
        # Hospitality Food Services (3)
        ("Hospitality and Food Services", "Event Catering", "Full catering with staff", 750, "Event Catering Service", 2),
        ("Hospitality and Food Services", "Catering Support", "Dedicated catering team", 680, "Event Catering Service", 2),
        ("Hospitality and Food Services", "Concierge Services", "Guest services and coordination", 350, "Event Decoration Service", 2),
        
        # Laundry & Cleaning (2)
        ("Laundry And Cleaning Services", "Laundry Care", "Professional clothing care", 150, "Cleaning Service", 2),
        ("Laundry And Cleaning Services", "Full Laundry Service", "Complete laundry and cleaning", 180, "Cleaning Service", 2),
        
        # Manufacturing (1)
        ("Manufacturing & Technology", "Electronics Assembly", "Industrial electronics manufacturing", 900, "Small Construction Project", 2),
        
        # Mechanical & Repair (4)
        ("Mechanical And Repair Services", "Auto Engine Repair", "Engine repair and maintenance", 520, "Car Maintenance Service", 2),
        ("Mechanical And Repair Services", "Bicycle Repair", "Bike maintenance and repair", 120, "Car Maintenance Service", 2),
        ("Mechanical And Repair Services", "Undercarriage Repair", "Vehicle suspension work", 480, "Car Maintenance Service", 2),
        ("Mechanical And Repair Services", "Bicycle Mechanics", "Advanced bike servicing", 200, "Car Maintenance Service", 2),
        
        # Printing Service (2)
        ("Printiing Service", "Printing Services", "Digital and offset printing", 300, "Website Design Service", 2),
        ("Printiing Service", "Advanced Printing", "Specialty printing solutions", 450, "Website Design Service", 2),
        
        # Repair & Technical (4)
        ("Repair And Technical Services", "Computer Support", "IT helpdesk and support", 250, "Website Design Service", 2),
        ("Repair And Technical Services", "Garage Maintenance", "Vehicle service and repairs", 400, "Car Maintenance Service", 2),
        ("Repair And Technical Services", "Computer Repair", "Hardware and software fixes", 300, "Website Design Service", 2),
        ("Repair And Technical Services", "IT Support", "Network and system support", 350, "Website Design Service", 2),
        
        # Skilled Trades Plumbing (4)
        ("Skilled Trades And Plumbing", "Woodworking", "Custom wood projects", 600, "Small Construction Project", 2),
        ("Skilled Trades And Plumbing", "Waterproofing", "Moisture and water protection", 500, "Small Construction Project", 2),
        ("Skilled Trades And Plumbing", "Industrial Plumbing", "Commercial plumbing systems", 800, "Basic Plumbing Service", 2),
        ("Skilled Trades And Plumbing", "Residential Plumbing", "Home plumbing services", 350, "Basic Plumbing Service", 2),
        
        # Skilled Trades Systems (4)
        ("Skilled Trades And Systems", "HVAC Services", "Heating and cooling systems", 650, "AC Repair Service", 2),
        ("Skilled Trades And Systems", "Waterproofing Service", "Basement and concrete sealing", 480, "Small Construction Project", 2),
        ("Skilled Trades And Systems", "Pipe Fitting", "Professional pipe installation", 420, "Basic Plumbing Service", 2),
        ("Skilled Trades And Systems", "Building Systems Upgrade", "Seeking comprehensive building upgrades", 5000, "Small Construction Project", 2),
        
        # Specialized Personal (3)
        ("Specialized Personal Services", "Personal Fitness", "Certified fitness training", 280, "AC Repair Service", 2),
        ("Specialized Personal Services", "Pet Care", "Grooming and pet services", 200, "AC Repair Service", 2),
        ("Specialized Personal Services", "Team Building", "Corporate team activities", 400, "Website Design Service", 2),
        
        # Technical IT (2)
        ("Technical Maintenance and IT", "Robotics Maintenance", "Industrial automation support", 800, "Small Construction Project", 2),
        ("Technical Maintenance and IT", "IT Hardware Repair", "Server and network support", 350, "Website Design Service", 2),
        
        # Video Editing (2)
        ("Video Editing", "Video Editing", "Professional video post-production", 450, "Website Design Service", 2),
        ("Video Editing", "Video Production", "Complete video production services", 500, "Website Design Service", 2),
        
        # Web Design (1)
        ("Web Designing", "Web Design", "Professional web development", 1200, "Website Design Service", 3),
    ]

def generate_reviews_for_listing(listing_type):
    """Generate 2-5 reviews for a listing"""
    reviews = []
    review_count = random.randint(2, 5)
    
    review_templates = {
        "service": [
            {"rating": 5, "title": "Excellent work!", "comment": "Highly professional and punctual. Will hire again!"},
            {"rating": 5, "title": "Highly recommended", "comment": "Great service, exactly as described. Very satisfied."},
            {"rating": 4, "title": "Good quality", "comment": "Good work, minor issues but overall satisfied."},
            {"rating": 5, "title": "Perfect!", "comment": "Exceeded my expectations. Outstanding service!"},
            {"rating": 4, "title": "Reliable and professional", "comment": "Good communication and reliable service."},
        ],
        "offer": [
            {"rating": 5, "title": "Great offer!", "comment": "Very interested in this service. Excellent pricing!"},
            {"rating": 5, "title": "Perfect match", "comment": "Exactly what I was looking for. Highly interested!"},
            {"rating": 4, "title": "Interested", "comment": "Good offer, would like to know more details."},
            {"rating": 5, "title": "Interested", "comment": "This is what I need. Great opportunity!"},
            {"rating": 4, "title": "Good opportunity", "comment": "Looks promising. Interested in learning more."},
        ]
    }
    
    templates = review_templates.get(listing_type, review_templates["service"])
    for _ in range(review_count):
        template = random.choice(templates)
        reviews.append({
            "rating": template["rating"],
            "title": template["title"],
            "comment": template["comment"],
            "tags": ["professional", "reliable"] if listing_type == "service" else ["interested", "relevant"],
            "is_verified_purchase": True
        })
    
    return reviews

def main():
    print("=" * 70)
    print("ENHANCED SEEDER GENERATION PIPELINE v2")
    print("=" * 70)
    
    # Step 1: Scan directory
    print("\nStep 1: Scanning listing_seeder directory...")
    categories_data = scan_listing_seeder()
    if not categories_data:
        print("Failed to scan directory")
        return
    
    total_images = sum(len(images) for images in categories_data.values())
    print(f"  Found {len(categories_data)} categories with {total_images} images")
    
    # Step 2: Load workflows
    print("\nStep 2: Loading workflow definitions...")
    workflows = get_workflow_definitions()
    print(f"  Loaded {len(workflows)} workflows")
    
    # Step 3: Create listings from definitions
    print("\nStep 3: Creating listings with multiple images...")
    listings_data = get_listing_definitions()
    print(f"  Processing {len(listings_data)} listings...")
    
    # Create categories_and_listings structure
    images_and_categories = {}
    image_pool = {}
    
    # First, create a pool of images by category (keeping original category names)
    for clean_name, images in categories_data.items():
        image_pool[clean_name] = images.copy()
    
    # Now assign images to listings
    created_listings = 0
    all_listings = []
    
    for category, name, desc, price, wf_name, images_per_listing in listings_data:
        if category not in image_pool or len(image_pool[category]) == 0:
            print(f"  ⚠ No images available for {category}")
            continue
        
        # Pick 2-3 random images from this category
        available_images = image_pool[category]
        images_count = min(images_per_listing, len(available_images))
        selected_images = random.sample(available_images, images_count)
        
        # Create listing entry with ALL selected images (no type yet)
        listing_entry = {
            "category": category,
            "listing_name": name,
            "listing_description": desc,
            "listing_price_or_budget": price,
            "workflow_name": wf_name,
            "workflow_steps": workflows.get(wf_name, []),
            "images": selected_images  # Multiple images per listing
        }
        
        all_listings.append(listing_entry)
        created_listings += 1
        
        # Remove used images from pool
        for img in selected_images:
            available_images.remove(img)
    
    print(f"  Successfully created {created_listings} listings")
    
    # Step 4: Balance services and offers to 70/30 split
    print("\nStep 4: Balancing to 70/30 split (70% services, 30% offers)...")
    split_point = int(created_listings * 0.70)  # 70% for services
    
    for idx, listing in enumerate(all_listings):
        if idx < split_point:
            listing['listing_type'] = 'service'
            listing['reviews'] = generate_reviews_for_listing('service')
        else:
            listing['listing_type'] = 'offer'
            listing['reviews'] = generate_reviews_for_listing('offer')
    
    # Now rebuild images_and_categories with types assigned
    images_and_categories = {}
    for listing in all_listings:
        category = listing.pop('category')
        if category not in images_and_categories:
            images_and_categories[category] = []
        images_and_categories[category].append(listing)
    
    # Step 5: Calculate distribution
    print("\nStep 5: Distribution summary (AFTER balancing):")
    service_count = sum(1 for cat_listings in images_and_categories.values() 
                       for listing in cat_listings if listing['listing_type'] == 'service')
    offer_count = sum(1 for cat_listings in images_and_categories.values() 
                     for listing in cat_listings if listing['listing_type'] == 'offer')
    
    print(f"  Services: {service_count} ({service_count/(service_count+offer_count)*100:.1f}%)")
    print(f"  Offers: {offer_count} ({offer_count/(service_count+offer_count)*100:.1f}%)")
    print(f"  Total: {service_count + offer_count} listings")
    
    # Step 5: Create seeder.json structure
    print("\nStep 5: Creating seeder.json structure...")
    seeder_structure = {
        "_instructions": {
            "version": "2.0",
            "created_at": datetime.now().isoformat(),
            "description": "Auto-generated seeder configuration for listings with multiple images",
            "note": "All listings have 2-3 images each. 70/30 service-to-offer ratio maintained."
        },
        "dir_path": os.path.join(os.path.dirname(os.path.dirname(SCRIPT_DIR)), "listing_seeder"),
        "settings": {
            "auto_create_categories": True,
            "skip_duplicates": True
        },
        "images_and_categories": images_and_categories
    }
    
    # Step 6: Save seeder.json
    print("Step 6: Saving seeder.json...")
    output_file = os.path.join(SCRIPT_DIR, "seeder.json")
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(seeder_structure, f, indent=2, ensure_ascii=False)
    
    print("\n" + "=" * 70)
    print("SUCCESS! seeder.json is ready!")
    print("=" * 70)
    print(f"\nSummary:")
    print(f"  Services: {service_count} ({service_count/(service_count+offer_count)*100:.1f}%)")
    print(f"  Offers: {offer_count} ({offer_count/(service_count+offer_count)*100:.1f}%)")
    print(f"  Total: {service_count + offer_count} listings")
    print(f"  Categories: {len(images_and_categories)}")
    print(f"  Average images per listing: 2-3")
    print(f"\nNext step:")
    print(f"  php artisan seed:from-json --file=docs/seeding/seeder.json")
    print("=" * 70)

if __name__ == "__main__":
    main()
