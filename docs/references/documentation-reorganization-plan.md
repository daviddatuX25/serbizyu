# Documentation Organization Plan

## Current Status
- **79 Markdown files** scattered across root directory
- **Organized folders**: `docs/`, `project-context/`, `progress-context/`, `Project essential/`, `developer notes/`
- **Goal**: Centralize all docs in `docs/` with proper structure

## Proposed Structure

```
docs/
├── README.md                          # Main documentation hub
├── QUICK_START.md                     # Getting started guide
├── architecture/
│   ├── ARCHITECTURE_DIAGRAMS.md
│   ├── PROJECT_ESSENTIAL/
│   │   ├── master_plan.md
│   │   ├── PROJECT_STATUS.md
│   │   └── DEVELOPMENT_GUIDELINES.md
│   └── NEXT_STEPS_ARCHITECTURE.md
├── guides/
│   ├── QUICK_START_GUIDE.md
│   ├── HOW_TO_MESSAGE_GUIDE.md
│   ├── FAQ.md
│   └── WORK_CATALOG_CLARIFICATION.md
├── features/
│   ├── admin/
│   │   ├── ADMIN_SYSTEM_IMPLEMENTATION.md
│   │   └── ADMIN_SYSTEM_COMPLETION.md
│   ├── messaging/
│   │   ├── MESSAGING_SYSTEM_COMPLETE.md
│   │   ├── MESSAGING_IMPLEMENTATION_PLAN.md
│   │   ├── MESSAGING_QUICK_REFERENCE.md
│   │   ├── MESSAGING_USER_GUIDE.md
│   │   └── MESSAGING_SYSTEM_CONFIGURATION_GUIDE.md
│   ├── payments/
│   │   ├── PAYMENT_SYSTEM_SETUP.md
│   │   ├── README_PAYMENT_SYSTEM.md
│   │   ├── PAYMENT_QUICK_REFERENCE.md
│   │   └── PAYMENT_SETTLEMENT_INTEGRATION.md
│   ├── orders/
│   │   ├── ORDER_SYSTEM_UPDATES.md
│   │   ├── ORDER_WORK_INTEGRATION_COMPLETE.md
│   │   └── INTEGRATION_PLAN_ORDER_WORK_REVIEWS.md
│   ├── reviews/
│   │   ├── REVIEW_SYSTEM_IMPLEMENTATION.md
│   │   ├── REVIEW_SYSTEM_COMPLETE_REFERENCE.md
│   │   └── REVIEW_SYSTEM_FRONTEND_INTEGRATION.md
│   ├── flags/
│   │   └── FLAGGING_IMPLEMENTATION_PLAN.md
│   └── work-visibility/
│       ├── WORK_VISIBILITY_IMPLEMENTATION.md
│       ├── WORK_VISIBILITY_COMPLETE_SUMMARY.md
│       └── WORK_VISIBILITY_QUICK_REFERENCE.md
├── implementation/
│   ├── IMPLEMENTATION_COMPLETE.md
│   ├── IMPLEMENTATION_STATUS_COMPLETE.md
│   ├── INTEGRATION_PLANNING_COMPLETE.md
│   └── INTEGRATION_SUMMARY_FOR_DEVELOPER.md
├── seeding/
│   ├── SEEDING_SYSTEM.md              # New comprehensive guide
│   ├── SEEDING_PIPELINE_GUIDE.md
│   ├── SEEDER_JSON_STRUCTURE_ANALYSIS.md
│   └── SEEDFROMJSON_REFACTORING_SUMMARY.md
├── progress/
│   ├── DELIVERY_SUMMARY.md
│   ├── MILESTONE_2.2_COMPLETION.md
│   ├── MVP_COMPLETION_REPORT.md
│   └── REDUNDANCIES_ANALYSIS.md
└── reference/
    ├── DOCUMENTATION_INDEX.md
    ├── PLANNING_DOCUMENTS_INDEX.md
    └── CLAUDE.md
```

## Files to Archive (Keep but move to archive/)
- MESSAGING_VISUAL_GUIDE.md
- MESSAGING_TEST_GUIDE.md
- MESSAGING_SYSTEM_DEEP_PLAN_INDEX.md
- MESSAGING_PHASE_4_COMPLETE.md
- MESSAGING_INTEGRATION_DEEP_PLAN.md
- MESSAGING_ACTION_ITEMS.md
- MESSAGING_EXECUTIVE_SUMMARY.md
- WORK_VISIBILITY_VERIFICATION.md
- WORK_VISIBILITY_INDEX.md
- WORK_VISIBILITY_CODE_CHANGES.md
- XENDIT_CASH_PAYMENT_IMPLEMENTATION.md
- ORDER_WORK_REVIEW_INTEGRATION_FINAL.md
- ORDER_WORK_INTEGRATION_SCAN.md
- ORDER_WORK_INTEGRATION_PHASES.md
- ORDER_WORK_INTEGRATION_IMPLEMENTATION.md
- OPTIMIZATION_PLAN.md

## Action Items
1. Create new seeding documentation combining all seeding files
2. Move files to organized structure
3. Update cross-references in README
4. Remove duplicates/outdated versions
