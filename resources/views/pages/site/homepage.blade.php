@extends('pages.site.layout')

@section('title', 'ShopDemo – Simple ecommerce demo')

@section('content')
    {{-- White hero; header uses stone-100 so this reads as the brighter "white" band --}}
    <section
        class="relative isolate flex min-h-[calc(100dvh-4rem)] w-full flex-col justify-center overflow-hidden bg-white py-14 lg:py-0"
        aria-labelledby="home-hero-heading"
    >
        {{-- Soft atmosphere (no blue) --}}
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <div
                class="absolute -left-1/4 top-0 h-[42rem] w-[42rem] rounded-full opacity-[0.2] blur-3xl"
                style="background: radial-gradient(circle at center, var(--color-accent) 0%, transparent 65%);"
            ></div>
            <div
                class="absolute -right-1/4 bottom-0 h-[36rem] w-[36rem] rounded-full bg-stone-200/40 blur-3xl"
            ></div>
            <div
                class="absolute inset-0 opacity-[0.35]"
                style="background-image: linear-gradient(rgba(15, 23, 42, 0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(15, 23, 42, 0.06) 1px, transparent 1px); background-size: 48px 48px;"
            ></div>
            <div class="absolute inset-0 bg-gradient-to-b from-white via-white to-stone-50/40"></div>
        </div>

        {{-- Same shell as header: logo + nav + actions --}}
        <div class="relative z-10 mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-stone-200 bg-stone-50 px-3 py-1 text-[11px] font-medium uppercase tracking-[0.2em] text-slate-600">
                <span class="h-1.5 w-1.5 rounded-full" style="background-color: var(--color-accent);"></span>
                Demo storefront
            </p>
            <h1 id="home-hero-heading" class="text-balance text-4xl font-semibold tracking-tight text-slate-900 sm:text-5xl lg:text-[2.75rem] lg:leading-[1.08]">
                Curated commerce,
                <span class="relative inline-block">
                    <span class="relative z-10 text-slate-900">without the noise.</span>
                    <span
                        class="absolute -bottom-1 left-0 h-3 w-full -skew-x-6 opacity-90"
                        style="background: linear-gradient(90deg, var(--color-accent), transparent);"
                        aria-hidden="true"
                    ></span>
                </span>
            </h1>
            <p class="mt-5 w-full max-w-none text-pretty text-sm leading-relaxed text-slate-600 sm:text-base">
                A compact Laravel shop you can extend: catalog browsing, auth-gated cart, and checkout scaffolding—styled as a bold landing so the first screen feels intentional, not empty.
            </p>
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a
                    href="/products"
                    class="group inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-stone-900/10 transition hover:brightness-110"
                    style="background-color: var(--color-accent);"
                >
                    Browse products
                    <span class="transition-transform group-hover:translate-x-0.5" aria-hidden="true">→</span>
                </a>
                <a
                    href="/cart"
                    class="inline-flex items-center rounded-lg border border-stone-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-800 shadow-sm transition hover:border-stone-400 hover:bg-stone-50"
                >
                    Open cart
                </a>
            </div>
            <dl class="mt-10 grid w-full grid-cols-3 gap-6 border-t border-stone-200 pt-8 text-left sm:gap-10 lg:gap-16">
                <div>
                    <dt class="text-[10px] font-medium uppercase tracking-wider text-slate-500">Stack</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">Laravel</dd>
                </div>
                <div>
                    <dt class="text-[10px] font-medium uppercase tracking-wider text-slate-500">UI</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">Tailwind</dd>
                </div>
                <div>
                    <dt class="text-[10px] font-medium uppercase tracking-wider text-slate-500">Focus</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-900">Cart flow</dd>
                </div>
            </dl>
        </div>

        {{-- Transition into page background (slate-50) --}}
        <div class="pointer-events-none absolute bottom-0 left-0 right-0 h-12 text-slate-50" aria-hidden="true">
            <svg class="h-full w-full" preserveAspectRatio="none" viewBox="0 0 1440 48" fill="currentColor">
                <path d="M0 48h1440V0C1200 32 720 48 0 24v24Z" opacity="0.6" />
            </svg>
        </div>
    </section>
@endsection
