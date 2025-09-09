# Claude Code - Rainmaker Project Documentation

## 📋 **Project Overview**
Rainmaker is a Laravel-based investment research platform using Filament v4.0 for the admin interface. This project manages companies, research items, categories, and tags with a focus on streamlined user experience.

## 🏗️ **Current Tech Stack**
- **Framework**: Laravel 12.x
- **Admin Panel**: Filament v4.0
- **Database**: PostgreSQL
- **Frontend**: Livewire + Alpine.js + Tailwind CSS (TALL stack)
- **Media**: Spatie Laravel MediaLibrary
- **Permissions**: Spatie Laravel Permission

## 🗄️ **Database Models**
- **Company**: Investment companies with ticker symbols, market cap, sectors
- **ResearchItem**: Research notes/documents linked to companies and categories
- **Category**: Organizational categories for research items
- **Tag**: Flexible tagging system with colors
- **User**: System users with authentication

## 🔧 **Development Notes**

### **Filament v4.0 Implementation Status**
- ✅ **Fixed Form Schemas**: Updated from deprecated v3 syntax to proper v4.0 schema structure
- ✅ **Fixed Actions**: Corrected imports from `Filament\Tables\Actions` to `Filament\Actions`
- ✅ **Fixed Table Methods**: Updated from `->actions()` to `->recordActions()` and `->bulkActions()` to `->toolbarActions()`
- ✅ **Fixed Database Constraints**: Added `mutateFormDataBeforeCreate()` methods to handle `created_by` fields
- ✅ **Working Resources**: CompanyResource, TagResource, ResearchItemResource, CategoryResource, TestResource

### **Current Issues Resolved**
1. **Form Schema Structure**: Removed `Form::make()` wrapper, using direct `$schema->components([])`
2. **Action Namespaces**: Actions now import from `Filament\Actions` instead of `Filament\Tables\Actions`
3. **User Association**: Automatically sets `created_by`/`user_id` on record creation
4. **Validation**: Proper unique constraints and field validation implemented

## 🚀 **Planned Architecture Migration**

### **📄 Current Implementation Plan: [FILAMENT_SPA_PLAN.md](./FILAMENT_SPA_PLAN.md)**

The project is planned to migrate from traditional Filament resources to a streamlined SPA-like experience:

#### **From Traditional Resources → To Streamlined Components**
```
OLD APPROACH:                    NEW APPROACH:
/admin/companies                 → Single-page with modals
/admin/companies/create          → Create modal
/admin/companies/{id}/edit       → Edit modal
/admin/research-items           → Integrated table component
/admin/research-items/create    → Modal with relationship selection
```

#### **Key Improvements Planned**
- **Modal-first editing** instead of separate pages
- **Inline table actions** for quick updates
- **Dashboard-style layouts** with widgets
- **Real-time updates** with Livewire
- **Bulk operations** via modal interfaces

## 📁 **Project Structure**

### **Current Filament Resources**
```
app/Filament/Resources/
├── Companies/
│   ├── CompanyResource.php          ✅ Working
│   ├── Pages/CreateCompany.php      ✅ Fixed created_by
│   └── Pages/EditCompany.php
├── ResearchItems/
│   ├── ResearchItemResource.php     ✅ Working
│   └── Pages/CreateResearchItem.php ✅ Fixed user_id
├── Tags/
│   ├── TagResource.php              ✅ Working
│   └── Pages/CreateTag.php          ✅ Fixed created_by
├── Categories/
│   └── CategoryResource.php         ✅ Working
└── Tests/
    └── TestResource.php             ✅ Working
```

### **Planned Livewire Components** (From SPA Plan)
```
app/Livewire/
├── Dashboard/
│   ├── InvestmentDashboard.php     🔄 Planned
│   └── CompanyOverview.php         🔄 Planned
├── Management/
│   ├── CompanyManagement.php       🔄 Planned - Priority 1
│   ├── ResearchItemManagement.php  🔄 Planned - Priority 2
│   └── TagManagement.php           🔄 Planned - Priority 3
└── Widgets/
    ├── CompanyStatsWidget.php      🔄 Planned
    ├── RecentActivityWidget.php    🔄 Planned
    └── QuickActionsWidget.php      🔄 Planned
```

## 🛠️ **Development Commands**

### **Common Laravel Commands**
```bash
# Development server with queue and logs
composer run dev

# Run tests
composer run test

# Generate Filament resources
php artisan make:filament-resource ModelName --generate

# Generate Livewire components
php artisan make:livewire ComponentName

# Generate widgets
php artisan make:filament-widget WidgetName
```

### **Database Operations**
```bash
# Run migrations
php artisan migrate

# Fresh database with seed
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_table_name
```

## 🔍 **Key Features Implemented**

### **Company Management**
- ✅ Ticker symbol validation and uniqueness
- ✅ Market cap formatting and display
- ✅ Sector and industry categorization
- ✅ Creator tracking

### **Research Items**
- ✅ Company association with dropdowns
- ✅ Category and tag relationships
- ✅ Visibility levels (public, team, private)
- ✅ File attachments with Spatie MediaLibrary
- ✅ Markdown content editor

### **Tag System**
- ✅ Color-coded tags
- ✅ Creator tracking
- ✅ Many-to-many with research items

## 🎯 **Next Development Priorities**

Based on [FILAMENT_SPA_PLAN.md](./FILAMENT_SPA_PLAN.md):

1. **🔄 Create CompanyManagement Livewire component** - Single page with table + modal actions
2. **🔄 Create ResearchItemManagement component** - Modal-based creation with relationships
3. **🔄 Create TagManagement component** - Simple modal CRUD with inline editing
4. **🔄 Build Investment Research Dashboard** - Widget-based overview
5. **🔄 Update routing** - Streamlined routes for new interface
6. **🔄 Testing & refinement** - Ensure smooth UX

## 🐛 **Known Issues & Solutions**

### **Resolved Issues**
- ✅ **Filament Actions not found**: Fixed imports to use `Filament\Actions`
- ✅ **Form schemas not working**: Updated to v4.0 syntax
- ✅ **Database constraint violations**: Added user tracking in create methods
- ✅ **Table actions not displaying**: Fixed action method names

### **Current Status**
- ✅ All traditional Filament resources are working
- ✅ Forms create records successfully  
- ✅ Table actions (edit, delete) functional
- ✅ Validation and uniqueness constraints working
- 🔄 Ready to implement SPA-like interface

## 📚 **Documentation References**
- [Filament SPA Implementation Plan](./FILAMENT_SPA_PLAN.md) - Comprehensive migration strategy
- [Filament v4.0 Documentation](https://filamentphp.com/docs/4.x) - Official Filament docs
- [Laravel Documentation](https://laravel.com/docs) - Framework reference

## 💡 **Development Tips**

### **When Working with Filament v4.0**
- Always use `$schema->components([])` directly, not `Form::make()`
- Import actions from `Filament\Actions`, not `Filament\Tables\Actions`
- Use `->recordActions()` for table row actions
- Use `->toolbarActions()` for bulk actions
- Add user tracking in `mutateFormDataBeforeCreate()` methods

### **For SPA Implementation**
- Leverage Livewire traits: `HasTable`, `HasActions`, `HasSchemas`
- Use modal actions for create/edit operations
- Implement real-time updates with Livewire properties
- Consider Alpine.js for enhanced client-side interactions

---

*Last Updated: 2025-09-07*  
*This documentation serves as a comprehensive guide for Claude Code when working on the Rainmaker project.*