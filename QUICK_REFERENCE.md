# Quick Reference Guide - Premium SaaS Dashboard

## 🎯 Component Quick Copy-Paste

### Button Variants

```blade
<!-- Primary Button -->
<button class="btn btn-primary">Action</button>

<!-- Secondary Button -->
<button class="btn btn-secondary">Cancel</button>

<!-- Danger Button -->
<button class="btn btn-danger">Delete</button>

<!-- Small Button -->
<button class="btn btn-primary btn-sm">Edit</button>

<!-- Large Button -->
<button class="btn btn-primary btn-lg">Create Account</button>

<!-- With Icon -->
<button class="btn btn-primary">
  <svg class="w-5 h-5">...</svg>
  <span>Action</span>
</button>

<!-- Disabled State -->
<button class="btn btn-primary disabled:opacity-50 disabled:cursor-not-allowed">Action</button>
```

---

## Form Components

### Input Fields

```blade
<!-- Text Input -->
<div class="form-group">
  <label class="form-label" for="name">Label</label>
  <input type="text" id="name" class="input-base" placeholder="Placeholder">
</div>

<!-- Email Input -->
<input type="email" class="input-base" placeholder="email@example.com">

<!-- Number Input -->
<input type="number" step="0.01" class="input-base" placeholder="0.00">

<!-- With Error -->
<input type="text" class="input-base input-error">
<p class="form-help text-red-600">Error message</p>

<!-- With Help Text -->
<input type="text" class="input-base">
<p class="form-help">Optional help text</p>

<!-- Disabled -->
<input type="text" class="input-base disabled:bg-slate-50">
```

### Select Fields

```blade
<select class="input-base">
  <option>Select option</option>
  <option>Option 1</option>
  <option>Option 2</option>
</select>
```

### Textarea

```blade
<textarea class="input-base resize-none" rows="4"></textarea>
```

---

## Cards & Containers

```blade
<!-- Basic Card -->
<div class="card p-6">
  Content here
</div>

<!-- Elevated Card -->
<div class="card-elevated p-6">
  Content here
</div>

<!-- Premium Card -->
<div class="card-premium p-8">
  Content here
</div>

<!-- Stat Card -->
<div class="stat-card group">
  <div class="stat-card-icon">
    <svg>...</svg>
  </div>
  <p class="stat-card-label">Label</p>
  <p class="stat-card-value">1,234</p>
</div>
```

---

## Badges & Status Indicators

```blade
<!-- Primary Badge -->
<span class="badge badge-primary">Primary</span>

<!-- Success Badge -->
<span class="badge badge-success">Approved</span>

<!-- Warning Badge -->
<span class="badge badge-warning">Pending</span>

<!-- Danger Badge -->
<span class="badge badge-danger">Rejected</span>

<!-- Neutral Notification -->
<span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
```

---

## Tables

```blade
<div class="table-wrapper overflow-x-auto">
  <table class="w-full">
    <thead class="table-head">
      <tr>
        <th class="table-cell font-semibold text-slate-700">Header</th>
        <th class="table-cell font-semibold text-slate-700">Header</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
      <tr class="table-body-row">
        <td class="table-cell py-4">Cell</td>
        <td class="table-cell py-4">Cell</td>
      </tr>
    </tbody>
  </table>
</div>
```

---

## Empty States

```blade
<div class="flex flex-col items-center justify-center py-12">
  <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
    <svg class="w-8 h-8 text-slate-400">...</svg>
  </div>
  <h3 class="text-lg font-semibold text-slate-900 mb-1">No items yet</h3>
  <p class="text-slate-600 text-center mb-6 max-w-sm">Description of empty state</p>
  <a href="#" class="btn btn-primary">Create first item</a>
</div>
```

---

## Animations

### Using Animations

```blade
<!-- Fade In -->
<div class="animate-fade-in">Content</div>

<!-- Slide Up -->
<div class="animate-slide-up">Content</div>

<!-- Pulse Soft -->
<div class="animate-pulse-soft">Content</div>

<!-- Smooth Transition -->
<div class="transition-smooth hover:bg-slate-100">Content</div>
```

---

## Typography

```blade
<!-- Heading 1 -->
<h1 class="text-4xl md:text-5xl font-bold text-slate-900">Title</h1>

<!-- Heading 2 -->
<h2 class="text-2xl md:text-3xl font-bold text-slate-900">Section</h2>

<!-- Heading 3 -->
<h3 class="text-xl md:text-2xl font-semibold text-slate-900">Subsection</h3>

<!-- Body Text -->
<p class="text-slate-600">Regular paragraph text</p>

<!-- Small Text -->
<p class="text-sm text-slate-500">Small description or help text</p>

<!-- Label -->
<label class="form-label">Label text</label>

<!-- Uppercase Small -->
<p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Category</p>
```

---

## Icons (SVG Examples)

### Action Icons

```blade
<!-- Plus/Add -->
<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
</svg>

<!-- Edit/Pencil -->
<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
</svg>

<!-- Delete/Trash -->
<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
</svg>

<!-- Check/Checkmark -->
<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
</svg>

<!-- Arrow Right -->
<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
</svg>

<!-- Settings/Gear -->
<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
</svg>

<!-- Logout -->
<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
</svg>
```

---

## Layout Patterns

### Page Header with Action

```blade
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
  <div>
    <h1 class="text-3xl font-bold text-slate-900">Page Title</h1>
    <p class="text-slate-600 mt-1">Subtitle or description</p>
  </div>
  <a href="#" class="btn btn-primary">Primary Action</a>
</div>
```

### Two-Column Grid

```blade
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="card-premium p-6">Left column</div>
  <div class="card-premium p-6">Right column</div>
</div>
```

### Three-Column Grid

```blade
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <div class="card-premium p-6">Column 1</div>
  <div class="card-premium p-6">Column 2</div>
  <div class="card-premium p-6">Column 3</div>
</div>
```

### Responsive Sidebar Layout

```blade
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2">Main content</div>
  <div>Sidebar</div>
</div>
```

---

## Gradient Buttons (Hero Actions)

```blade
<a href="#" class="group relative overflow-hidden bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-blue-500/20 hover:-translate-y-1">
  <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
  <div class="relative p-6 flex items-center gap-4 text-white">
    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-white/20 flex items-center justify-center">
      <svg>...</svg>
    </div>
    <div>
      <p class="font-semibold">Title</p>
      <p class="text-xs opacity-80">Subtitle</p>
    </div>
  </div>
</a>
```

---

## Color Reference

```blade
<!-- Blues (Primary) -->
class="bg-blue-600 text-blue-600 border-blue-200"

<!-- Sky (Secondary) -->
class="bg-sky-600 text-sky-600 border-sky-200"

<!-- Purple (Tertiary) -->
class="bg-purple-600 text-purple-600 border-purple-200"

<!-- Green (Success) -->
class="bg-green-600 text-green-600 border-green-200"

<!-- Amber (Warning) -->
class="bg-amber-500 text-amber-500 border-amber-200"

<!-- Red (Danger) -->
class="bg-red-500 text-red-500 border-red-200"

<!-- Gray (Neutral) -->
class="bg-slate-100 text-slate-600 border-slate-200"
```

---

## Responsive Helpers

```blade
<!-- Hide on mobile, show on sm and up -->
<div class="hidden sm:block">Desktop content</div>

<!-- Show on mobile only -->
<div class="sm:hidden">Mobile content</div>

<!-- Responsive spacing -->
<div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 lg:py-8">
  Content with responsive padding
</div>

<!-- Responsive grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 md:gap-6">
  Items
</div>

<!-- Responsive text size -->
<h1 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl">
  Responsive title
</h1>
```

---

## Tips & Tricks

1. **Consistent Spacing**: Use `gap-6` for all grids, `p-6` for cards
2. **Color Consistency**: Stick to the 6 main colors (Blue, Sky, Purple, Green, Amber, Red)
3. **Icon Sizing**: Use `w-5 h-5` for regular, `w-6 h-6` for large
4. **Shadows**: `shadow-sm` (subtle), `shadow-md` (default), `shadow-lg` (hover)
5. **Borders**: Always use `border-slate-200` for dividers unless colored
6. **Text Colors**: `text-slate-900` (headers), `text-slate-600` (body), `text-slate-500` (secondary)
7. **Animations**: Always add for interactive elements
8. **Accessibility**: Always include labels for form fields

---

## Variables to Use in Laravel

```blade
<!-- User Info -->
{{ auth()->user()->name }}
{{ auth()->user()->email }}

<!-- Dates -->
{{ now()->format('M d, Y') }}
{{ $date->format('M d') }}

<!-- Numbers -->
{{ number_format($amount, 2) }}
{{ number_format($count, 0) }}

<!-- Conditionals -->
@if($condition) ... @endif
@unless($condition) ... @endunless
@foreach($items as $item) ... @endforeach
```

---

Happy designing! This system scales beautifully and maintains consistency. 🎨✨
