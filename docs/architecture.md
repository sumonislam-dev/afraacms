Project Name: AfraaCMS

Tech Stack:
- Laravel (latest stable)
- Blade
- Tailwind CSS
- Alpine.js
- MySQL

Do NOT use:
- Livewire
- Filament
- Inertia
- Vue
- React

Architecture Rules:
- Follow SOLID principles.
- Keep controllers thin.
- Business logic belongs in Services under app/CMS/Services.
- Use FormRequest validation.
- Use Resource Controllers.
- Use Eloquent relationships.
- Use Blade Components.
- Use RESTful routes.
- Use repository pattern only when truly beneficial.
- Use Spatie Permission.
- Use Spatie Media Library.
- Every feature must be production-ready.
- Every migration must be reversible.
- Every module must be independently testable.
- Never break existing functionality.
