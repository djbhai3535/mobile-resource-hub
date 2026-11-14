<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Services\AdvertisementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdvertisementController extends Controller
{
    public function __construct(
        protected AdvertisementService $adService
    ) {}

    /**
     * List all advertisement campaigns.
     */
    public function index(Request $request): View
    {
        $placementFilter = $request->query('placement');
        $statusFilter = $request->query('status');

        $query = Advertisement::latest();

        if (!empty($placementFilter)) {
            $query->where('placement', $placementFilter);
        }

        if ($statusFilter === 'active') {
            $query->active();
        } elseif ($statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $advertisements = $query->paginate(15)->withQueryString();
        $placements = $this->adService->getPlacements();

        return view('admin.advertisements.index', compact('advertisements', 'placements', 'placementFilter', 'statusFilter'));
    }

    /**
     * Create Ad Form.
     */
    public function create(): View
    {
        $placements = $this->adService->getPlacements();

        return view('admin.advertisements.create', compact('placements'));
    }

    /**
     * Store new advertisement.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAdvertisement($request);

        // Security: Custom HTML snippets restricted to Super Admin only
        if (!empty($validated['custom_html']) && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Custom advertising code injection is restricted to Super Administrators.');
        }

        // Handle Image upload if provided
        if ($request->hasFile('creative_image')) {
            $file = $request->file('creative_image');
            $path = $file->store('advertisements', 'public');
            $validated['image_path'] = $path;
        }

        $validated['created_by'] = auth()->id();
        $validated['updated_by'] = auth()->id();

        $ad = Advertisement::create($validated);

        return redirect()->route('admin.advertisements.index')
            ->with('success', "Advertisement '{$ad->name}' created successfully.");
    }

    /**
     * Edit Ad Form.
     */
    public function edit(int $id): View
    {
        $advertisement = Advertisement::findOrFail($id);
        $placements = $this->adService->getPlacements();

        return view('admin.advertisements.edit', compact('advertisement', 'placements'));
    }

    /**
     * Update advertisement.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $ad = Advertisement::findOrFail($id);
        $validated = $this->validateAdvertisement($request, $ad->id);

        // Security check for custom HTML
        if (!empty($validated['custom_html']) && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized. Custom advertising code injection is restricted to Super Administrators.');
        }

        if ($request->hasFile('creative_image')) {
            $file = $request->file('creative_image');
            $path = $file->store('advertisements', 'public');
            $validated['image_path'] = $path;
        }

        $validated['updated_by'] = auth()->id();

        $ad->update($validated);

        return redirect()->route('admin.advertisements.index')
            ->with('success', "Advertisement '{$ad->name}' updated successfully.");
    }

    /**
     * Delete advertisement.
     */
    public function destroy(int $id): RedirectResponse
    {
        $ad = Advertisement::findOrFail($id);
        $name = $ad->name;
        $ad->delete();

        return redirect()->route('admin.advertisements.index')
            ->with('success', "Advertisement '{$name}' deleted successfully.");
    }

    /**
     * Placements Directory View.
     */
    public function placements(): View
    {
        $placements = $this->adService->getPlacements();
        $placementCounts = Advertisement::selectRaw('placement, count(*) as total, sum(case when is_active = 1 then 1 else 0 end) as active_count')
            ->groupBy('placement')
            ->get()
            ->keyBy('placement');

        return view('admin.advertisements.placements', compact('placements', 'placementCounts'));
    }

    /**
     * Analytics View.
     */
    public function analytics(Request $request): View
    {
        $days = (int) $request->query('days', 30);
        $overview = $this->adService->getAnalyticsOverview($days);

        return view('admin.advertisements.analytics', compact('overview', 'days'));
    }

    /**
     * Validate advertisement input data.
     */
    protected function validateAdvertisement(Request $request, ?int $adId = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'placement' => 'required|string|max:50',
            'type' => 'required|string|in:image,url,network,custom_html',
            'creative_image' => 'nullable|image|max:3072|mimes:jpeg,png,webp,gif',
            'image_alt' => 'nullable|string|max:255',
            'destination_url' => 'nullable|string|max:500',
            'custom_html' => 'nullable|string',
            'campaign_name' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'priority' => 'required|integer|min:1|max:10',
            'rotation_weight' => 'required|integer|min:1|max:100',
            'show_desktop' => 'sometimes|boolean',
            'show_tablet' => 'sometimes|boolean',
            'show_mobile' => 'sometimes|boolean',
            'show_light_theme' => 'sometimes|boolean',
            'show_dark_theme' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        // Default boolean checkboxes if not present in request
        $validated['show_desktop'] = $request->boolean('show_desktop', true);
        $validated['show_tablet'] = $request->boolean('show_tablet', true);
        $validated['show_mobile'] = $request->boolean('show_mobile', true);
        $validated['show_light_theme'] = $request->boolean('show_light_theme', true);
        $validated['show_dark_theme'] = $request->boolean('show_dark_theme', true);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Security: Destination URL must be HTTPS or internal route
        if (!empty($validated['destination_url']) && !$this->adService->isValidDestinationUrl($validated['destination_url'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'destination_url' => 'Destination URL must be a valid secure HTTPS link or relative internal path.',
            ]);
        }

        return $validated;
    }
}
