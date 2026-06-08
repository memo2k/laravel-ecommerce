<div id="demo-notice-wrapper">
    <script>
        if (localStorage.getItem('shopdemo_demo_notice_dismissed') === '1') {
            document.getElementById('demo-notice-wrapper').style.display = 'none';
        }
    </script>

    <div
        x-data="{
            dismissed: localStorage.getItem('shopdemo_demo_notice_dismissed') === '1',
            dismiss() {
                this.dismissed = true;
                localStorage.setItem('shopdemo_demo_notice_dismissed', '1');
            },
        }"
        x-show="!dismissed"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="border-b border-amber-200/80 bg-amber-50 px-4 py-1.5 text-[11px] sm:text-xs text-amber-950"
    >
        <div class="relative mx-auto flex max-w-7xl items-center justify-center pr-8 sm:pr-10">
            <p class="text-center">
                <span class="font-medium">Demo project</span>
                <span class="text-amber-800/80">·</span>
                All data resets every 24 hours
            </p>
            <button
                type="button"
                @click="dismiss()"
                class="absolute right-0 rounded p-1 text-amber-800/70 hover:bg-amber-100 hover:text-amber-950 transition-colors"
                aria-label="Dismiss notice"
            >
                <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
