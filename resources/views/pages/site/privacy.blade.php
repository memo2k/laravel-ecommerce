@extends('pages.site.layout')

@section('title', 'Privacy Notice – ShopDemo')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">
                Privacy Notice
            </h1>
            <p class="mt-2 text-sm text-slate-500">
                Last updated: {{ now()->format('F j, Y') }}
            </p>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-950 mb-8">
            <p class="font-medium">Demo site only</p>
            <p class="mt-2 leading-relaxed">
                ShopDemo is a portfolio project, not a real online store. It exists to demonstrate ecommerce and admin features.
                Please do not enter real payment details or sensitive personal information.
            </p>
        </div>

        <div class="space-y-8 text-sm leading-relaxed text-slate-700">
            <section>
                <h2 class="text-base font-semibold text-slate-900 mb-2">Data reset</h2>
                <p>
                    All site data is automatically reset every 24 hours at <strong>00:00 midnight</strong> (server time).
                    Accounts, orders, cart contents, and other stored information from the previous day are removed and replaced with fresh demo data.
                    Anything you submit may be lost at the next reset.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-slate-900 mb-2">Information we collect</h2>
                <p class="mb-3">When you use this demo, the application may store:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li><strong>Account data</strong> — name, email address, and a hashed password if you register or sign in.</li>
                    <li><strong>Profile and address data</strong> — delivery address, city, postal code, country, and phone number if you save them to your profile.</li>
                    <li><strong>Checkout data</strong> — name, email, phone, shipping address, order notes, and order history when you complete checkout.</li>
                    <li><strong>Cart data</strong> — products and quantities in your cart, linked to your account or browser session.</li>
                    <li><strong>Technical data</strong> — session cookies and similar data needed to keep you signed in, protect forms, and run the site.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-semibold text-slate-900 mb-2">How we use it</h2>
                <p>
                    Information is used only to operate this demo: authentication, cart and checkout flows, order confirmation emails, and admin dashboard features.
                    Data is not sold, rented, or used for advertising.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-slate-900 mb-2">Payments</h2>
                <p>
                    If card payment is enabled, checkout is processed by Stripe. Card numbers and payment credentials are handled by Stripe, not stored on this demo server.
                    Stripe’s own privacy policy applies to payment processing.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-slate-900 mb-2">Cookies and sessions</h2>
                <p>
                    The site uses essential cookies and session storage for login state, shopping cart functionality, and security (for example CSRF protection).
                    These are required for the demo to work and are not used for tracking across other websites.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-slate-900 mb-2">Your choices</h2>
                <ul class="list-disc pl-5 space-y-1">
                    <li>You can browse most of the storefront without creating an account.</li>
                    <li>Signed-in users can update profile details or delete their account from the profile page.</li>
                    <li>Because the database resets nightly, you should not rely on this site to keep any data long term.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-base font-semibold text-slate-900 mb-2">Ongoing development</h2>
                <p>
                    ShopDemo is an active portfolio project. Features, design, and behaviour may change as improvements are added and the site is updated regularly.
                    You may notice new functionality, layout changes, or temporary issues while work is in progress.
                </p>
            </section>

            <section>
                <h2 class="text-base font-semibold text-slate-900 mb-2">Changes to this notice</h2>
                <p>
                    This privacy notice may be updated as the demo evolves. The “Last updated” date at the top reflects the most recent revision.
                </p>
            </section>
        </div>
    </div>
@endsection
