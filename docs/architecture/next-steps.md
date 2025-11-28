# Seeding Pipeline - Next Steps & Architecture

## Current State ✅

**Phase 1-3 Complete:**
- ✅ Python script scans `listing_seeder/` and generates `seeder.json` template
- ✅ User fills JSON with listing data, workflow details, work catalog assignments
- ✅ Artisan command processes JSON and seeds Services/OpenOffers with images
- ✅ Randomized creator (user role only) and address assignment
- ✅ WorkCatalog and WorkTemplate creation from JSON
- ✅ Media attachment via Mediable with proper tagging

**Seeders Reorganized:**
- ✅ ListingsSeeder disabled (no more demo data cluttering database)
- ✅ Core seeders active: Categories, WorkCatalog, Workflows, UserReview, ServiceReview
- ✅ Clean database state ready for production data import

---

## Quick Start for Testing

### 1. Fresh Database with Core Seed Data
```bash
php artisan migrate:fresh --seed
# Creates: Users, Addresses, Categories, WorkCatalogs, Workflows, Reviews
# NO Services/OpenOffers (seeded via JSON instead)
```

### 2. Generate Seeding Template
```bash
python generate-seeder-json.py
# Scans listing_seeder/ folder
# Outputs: seeder.json with 23 images across categories
```

### 3. Edit seeder.json
- Fill `listing_type` (service/offer) for each image
- Fill `listing_name`, `listing_description`
- Set `price_or_budget`
- Define `workflow_name` and `workflow_steps`
- Assign `work_catalog` subset

### 4. Execute Seeding Pipeline
```bash
php artisan seed:from-seeder-json
# Creates: WorkCatalogs, Workflows, WorkTemplates, Services, OpenOffers
# Attaches: Images with proper Mediable tagging
# Randomizes: Creator (user role) and Address per listing
```

---

## What's Next? (Future Development)

### Phase 4: Admin UI for Workflow Management

**Goal**: Allow admins to manage workflows, work catalogs, and steps without editing JSON

**Features to Build:**
1. **Workflow Manager Dashboard**
   - List all workflows with their steps
   - Create/edit/delete workflows
   - Manage work catalog links
   - Reorder steps

2. **Work Catalog Manager**
   - CRUD interface for reusable work catalog items
   - Set descriptions and configuration
   - Track usage across workflows

3. **Bulk Import Interface**
   - Upload CSV/JSON files
   - Preview before importing
   - Mapping tool to match columns

4. **Media Management**
   - Upload images in bulk
   - Organize by category
   - Auto-generate thumbnails

### Phase 5: Listing Management UI

**Goal**: Allow users to create/edit services and open offers through UI instead of JSON

**Features to Build:**
1. **Service Editor**
   - Form to create service
   - Select workflow, category, address
   - Upload images
   - Set pricing

2. **OpenOffer Editor**
   - Form to create offer
   - Set budget, deadline
   - Upload images
   - Assign to user

3. **Listing Gallery**
   - Preview all services/offers
   - Edit/delete listings
   - View image attachments
   - Manage media

### Phase 6: Workflow Execution

**Goal**: Track progress through workflow steps during service delivery

**Features to Build:**
1. **Work Order System**
   - Create work order from service
   - Track status through workflow steps
   - Mark steps as complete
   - Add notes/comments per step

2. **Step Completion**
   - Capture work photos
   - Record time spent
   - Generate reports

3. **Integration with Reviews**
   - Link reviews to work steps
   - Include photo evidence

---

## Architecture Recommendations

### Database Extensions Needed

```sql
-- work_orders table (track service delivery progress)
CREATE TABLE work_orders (
    id PRIMARY KEY,
    service_id FOREIGN KEY,
    workflow_template_id FOREIGN KEY,
    user_id FOREIGN KEY (provider),
    status ENUM('pending', 'in_progress', 'completed', 'cancelled'),
    created_at, updated_at
);

-- work_order_steps table (track step completion)
CREATE TABLE work_order_steps (
    id PRIMARY KEY,
    work_order_id FOREIGN KEY,
    work_template_id FOREIGN KEY,
    status ENUM('pending', 'in_progress', 'completed'),
    completed_at NULLABLE,
    notes TEXT,
    created_at, updated_at
);

-- work_step_media table (attach photos to steps)
CREATE TABLE work_step_media (
    id PRIMARY KEY,
    work_order_step_id FOREIGN KEY,
    media_id FOREIGN KEY (Mediable),
    caption TEXT,
    created_at
);
```

### API Endpoints to Build

```
GET    /api/workflows              - List all workflows
POST   /api/workflows              - Create workflow
PUT    /api/workflows/{id}         - Update workflow
DELETE /api/workflows/{id}         - Delete workflow

GET    /api/workcatalogs           - List all work catalogs
POST   /api/workcatalogs           - Create work catalog
PUT    /api/workcatalogs/{id}      - Update work catalog

GET    /api/services               - List services
POST   /api/services               - Create service
PUT    /api/services/{id}          - Update service
DELETE /api/services/{id}          - Delete service

GET    /api/openoffers             - List open offers
POST   /api/openoffers             - Create open offer
PUT    /api/openoffers/{id}        - Update open offer

GET    /api/work-orders            - List work orders
POST   /api/work-orders            - Create work order (from service bid)
PUT    /api/work-orders/{id}       - Update work order status

POST   /api/work-orders/{id}/steps/{step}/complete  - Mark step complete
POST   /api/work-orders/{id}/steps/{step}/media     - Upload step photos
```

### Livewire Components to Build

```
Components/
├── WorkflowManager.php           - CRUD interface for workflows
├── WorkCatalogManager.php        - CRUD interface for catalogs
├── ListingEditor.php             - Create/edit services/offers
├── MediaUploader.php             - Bulk image upload
├── WorkOrderTracker.php          - Track service progress
└── StepCompletion.php            - Mark steps complete with photos
```

---

## Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                      Current Architecture                        │
└─────────────────────────────────────────────────────────────────┘

1. SETUP PHASE (php artisan migrate:fresh --seed)
   ├─ RolesSeeder         → Create user/admin/moderator roles
   ├─ UserSeeder          → Create sample users
   ├─ AddressSeeder       → Create service addresses
   ├─ CategorySeeder      → Create service categories
   ├─ WorkCatalogSeeder   → Create reusable work steps
   ├─ WorkflowSeeder      → Create workflow templates
   └─ ReviewSeeder        → Create sample reviews

2. DATA PREPARATION PHASE (python generate-seeder-json.py)
   ├─ Scan listing_seeder/ folder
   ├─ Extract images by category
   └─ Generate seeder.json with template

3. USER CONFIGURATION PHASE (Edit seeder.json)
   ├─ Fill listing_type (service/offer)
   ├─ Fill names & descriptions
   ├─ Define workflows & steps
   └─ Map work_catalog items

4. SEEDING PHASE (php artisan seed:from-seeder-json)
   ├─ Create WorkCatalogs (if new)
   ├─ Create Workflows & WorkTemplates
   ├─ Create Services/OpenOffers
   ├─ Attach images via Mediable
   ├─ Randomize creator & address
   └─ Generate summary report

┌─────────────────────────────────────────────────────────────────┐
│                    Future Architecture                           │
└─────────────────────────────────────────────────────────────────┘

5. ADMIN MANAGEMENT PHASE
   ├─ WorkflowManager UI
   ├─ WorkCatalogManager UI
   ├─ ListingEditor UI
   └─ MediaUploader UI

6. LISTING CREATION PHASE
   ├─ Service creation (via UI or API)
   ├─ OpenOffer creation (via UI or API)
   └─ Image attachment

7. WORK EXECUTION PHASE
   ├─ Create work orders from accepted bids
   ├─ Track step completion
   ├─ Capture photo evidence
   └─ Generate reports

8. REVIEW & ANALYTICS PHASE
   ├─ Review system integration
   ├─ Performance metrics
   ├─ Service completion rates
   └─ User ratings
```

---

## File Structure Reference

```
Project Root
├── generate-seeder-json.py           # Phase 1: Generate JSON template
├── seeder.json                        # Phase 2: User-filled configuration
│
├── app/Console/Commands/
│   └── SeedFromJson.php              # Phase 3: Process JSON and seed
│
├── database/seeders/
│   ├── DatabaseSeeder.php            # Main seeder orchestrator
│   ├── RolesSeeder.php               # Create roles
│   ├── UserSeeder.php                # Create users
│   ├── AddressSeeder.php             # Create addresses
│   ├── CategorySeeder.php            # Create categories
│   ├── WorkCatalogSeeder.php        # Create work catalogs
│   ├── WorkflowAndWorkTemplateSeeder.php  # Create workflows
│   ├── ListingsSeeder.php            # DISABLED (use JSON instead)
│   └── ReviewSeeder.php              # Create reviews
│
├── listing_seeder/                   # Image folder structure
│   ├── Construction/
│   ├── Plumbing/
│   ├── Electrical/
│   └── ...
│
├── storage/app/public/
│   ├── services/images/              # Service images (via Mediable)
│   └── open-offers/                  # OpenOffer images (via Mediable)
│
└── docs/
    └── SEEDING_PIPELINE_COMPLETE.md  # Full documentation
```

---

## Important Notes

### Why Disable ListingsSeeder?
- ✅ Prevents duplicate demo data cluttering production database
- ✅ Allows full control over seed data via seeder.json
- ✅ Enables clean separation: structure (categories/workflows) vs content (services/offers)
- ✅ Makes database reproducible from known configuration

### Why Keep Other Seeders Active?
- ✅ **CategorySeeder**: Foundational - services/offers need categories
- ✅ **WorkCatalogSeeder**: Defines reusable work steps across workflows
- ✅ **WorkflowSeeder**: Blueprint for service delivery process
- ✅ **UserSeeder**: Need users before seeding services/offers
- ✅ **ReviewSeeder**: Test review system (can be disabled if not needed)

### When to Run Each Phase

| Scenario | Commands |
|----------|----------|
| **Fresh Development** | `php artisan migrate:fresh --seed` → `python generate-seeder-json.py` → edit JSON → `php artisan seed:from-seeder-json` |
| **Add More Listings** | Update existing seeder.json → `php artisan seed:from-seeder-json` |
| **Reset to Clean State** | `php artisan migrate:fresh --seed` (clears all data) |
| **Dry-run Preview** | `php artisan seed:from-seeder-json --dry-run` |
| **Production Deploy** | `php artisan migrate` → manually upload seeder.json → `php artisan seed:from-seeder-json` |

---

## Performance Considerations

### Bulk Image Processing
- MediaUploader processes one image at a time
- For 1000+ images, consider:
  - Queue jobs to avoid timeout
  - Batch processing in chunks of 50
  - Progress reporting via artisan table

### Database Indexes
- Add indexes on `workflow_template_id`, `category_id`, `creator_id`
- Index `mediables` table for quick media lookups

### Storage Optimization
- Consider image compression/resizing
- Implement cleanup for orphaned media
- Regular backups of `storage/app/public/`

---

## Testing Checklist

- [ ] Run `php artisan migrate:fresh --seed` - no errors
- [ ] Verify no Services/OpenOffers in database
- [ ] Verify Categories, WorkCatalogs, Workflows exist
- [ ] Run `python generate-seeder-json.py` - seeder.json created
- [ ] Verify seeder.json has all 23 images from listing_seeder/
- [ ] Edit seeder.json with test data
- [ ] Run `php artisan seed:from-seeder-json --dry-run` - preview works
- [ ] Run `php artisan seed:from-seeder-json` - full execution
- [ ] Verify Services created (check count)
- [ ] Verify OpenOffers created (check count)
- [ ] Verify images attached (check storage/app/public/)
- [ ] Verify creators are randomized (check user_ids are mixed)
- [ ] Verify addresses randomized (check address_ids are mixed)
- [ ] Check mediables table has proper tags (gallery/images)

---

## Questions to Consider Next

1. **Should we build an admin UI first, or focus on API endpoints?**
2. **Do we need real-time status tracking for work orders?**
3. **Should workflow steps generate automatic notifications?**
4. **Do we need approval workflows before starting work?**
5. **Should we track time/cost per workflow step?**

