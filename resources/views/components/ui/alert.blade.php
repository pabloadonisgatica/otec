@if (session('status'))

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">

        <div
            x-data="{ show: true }"
            x-show="show"
            x-transition
            class="rounded-lg border border-green-200 bg-green-50 p-4 flex items-center justify-between">

            <div class="flex items-center gap-3">

                <svg class="w-5 h-5 text-green-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"/>

                </svg>

                <span class="text-green-800">

                    {{ session('status') }}

                </span>

            </div>

            <button
                @click="show=false"
                class="text-green-600 hover:text-green-800">

                ✕

            </button>

        </div>

    </div>

@endif