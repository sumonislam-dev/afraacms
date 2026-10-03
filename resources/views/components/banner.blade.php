@props(['type', 'override' => null, 'pageTitle' => null])

@php
    // A truthy field in $override (e.g. a page's own banner image/eyebrow)
    // takes precedence over the shared, site-wide banner for this $type;
    // any field the override doesn't set falls back to the shared banner.
    // All keys are always present (defaulting to null) so the direct
    // $data[...] access below never hits an undefined array key.
    $data = array_filter($override ?? []) + (banner($type) ?? []) + [
        'id' => null, 'title' => null, 'subtitle' => null,
        'image_url' => null, 'button_text' => null, 'button_url' => null,
        'popup_frequency' => 'session', 'popup_pages' => 'all', 'popup_delay' => 0, 'version' => null,
    ];
    // A page banner always has content to show (the page's own title), even
    // with no eyebrow/image configured anywhere.
    $hasContent = $pageTitle || $data['title'] || $data['subtitle'] || $data['image_url'] || $data['button_text'] || $data['button_url'];
    // A "Homepage only" popup is left out of every other page entirely.
    if ($type === 'popup' && $data['popup_pages'] === 'home' && ! request()->routeIs('home')) {
        $hasContent = false;
    }
@endphp

@if ($hasContent)
    @if ($type === 'cta')
        <x-frontend.cta
            :heading="$data['title']"
            :subheading="$data['subtitle']"
            :button-text="$data['button_text']"
            :button-url="$data['button_url']"
        />
    @elseif ($type === 'popup')
        {{-- Dismissal memory per popup_frequency: "always" keeps none,
             "session" uses sessionStorage, "daily"/"once" use localStorage
             (storing when it was closed, so "daily" can expire it). The key
             includes the banner's version, so editing it shows it again. --}}
        <div
            x-data="{
                open: false,
                key: @js('banner-popup-dismissed-'.$data['id'].'-'.$data['version']),
                frequency: @js($data['popup_frequency']),
                delay: @js((int) $data['popup_delay']),
                store() { return this.frequency === 'session' ? sessionStorage : localStorage; },
                init() {
                    let dismissed = false;
                    try {
                        const at = this.frequency === 'always' ? null : this.store().getItem(this.key);
                        dismissed = at !== null && (this.frequency !== 'daily' || Date.now() - Number(at) < 86400000);
                    } catch (e) {}
                    if (dismissed) return;
                    setTimeout(() => {
                        this.open = true;
                        this.$nextTick(() => this.$refs.dialog.focus());
                    }, this.delay * 1000);
                },
                dismiss() {
                    this.open = false;
                    if (this.frequency === 'always') return;
                    try { this.store().setItem(this.key, String(Date.now())); } catch (e) {}
                },
            }"
            x-show="open"
            x-transition.opacity.duration.200ms
            x-effect="document.documentElement.classList.toggle('overflow-hidden', open)"
            @keydown.escape.window="open && dismiss()"
            style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/60 p-4 backdrop-blur-sm"
        >
            {{-- Focus goes to the dialog itself (not the close button), so
                 mouse visitors don't get a focus ring the moment it opens. --}}
            <div
                x-ref="dialog"
                x-show="open"
                x-transition:enter="transition duration-200 ease-out"
                x-transition:enter-start="scale-95 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
                @click.outside="dismiss()"
                role="dialog"
                aria-modal="true"
                tabindex="-1"
                @if ($data['title']) aria-labelledby="banner-popup-title-{{ $data['id'] }}" @else aria-label="{{ __('Announcement') }}" @endif
                class="relative flex max-h-[90vh] w-full max-w-md flex-col overflow-hidden rounded-2xl bg-white shadow-2xl outline-none"
            >
                <button type="button" @click="dismiss()" aria-label="{{ __('Close') }}" class="absolute right-3 top-3 z-10 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-ink-900 shadow-md transition hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                    <x-icon name="x-mark" class="h-5 w-5" />
                </button>

                @if ($data['image_url'])
                    <div class="relative aspect-video w-full shrink-0 bg-gray-100">
                        <img src="{{ $data['image_url'] }}" alt="" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                    </div>
                @endif

                <div class="overflow-y-auto px-6 pb-6 text-center sm:px-8 {{ $data['image_url'] ? 'pt-6' : 'pt-12' }}">
                    @if ($data['title'])
                        <h2 id="banner-popup-title-{{ $data['id'] }}" class="font-display text-2xl font-bold text-ink-900">{{ $data['title'] }}</h2>
                    @endif

                    @if ($data['subtitle'])
                        <p class="mt-3 line-clamp-4 text-gray-600">{{ $data['subtitle'] }}</p>
                    @endif

                    @if ($data['button_text'] && $data['button_url'])
                        {{-- Clicking through counts as dismissing it. --}}
                        <a href="{{ $data['button_url'] }}" @click="dismiss()" class="mt-6 block w-full rounded-full bg-brand-500 px-6 py-3 font-semibold text-white shadow-lg shadow-brand-500/30 transition hover:bg-brand-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2">
                            {{ $data['button_text'] }}
                        </a>

                        <button type="button" @click="dismiss()" class="mt-3 text-sm text-gray-500 underline-offset-4 hover:text-gray-700 hover:underline focus:outline-none focus-visible:underline">
                            {{ __('No thanks') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @else
        <x-frontend.banner
            :title="$data['title']"
            :subtitle="$data['subtitle']"
            :image-url="$data['image_url']"
            :button-text="$data['button_text']"
            :button-url="$data['button_url']"
            :page-title="$pageTitle"
        />
    @endif
@endif
