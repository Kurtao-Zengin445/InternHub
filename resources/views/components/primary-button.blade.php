<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-2xl border border-transparent bg-slate-950 px-5 py-3 text-sm font-semibold text-white shadow-sm transition duration-150 ease-in-out hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:bg-slate-900 disabled:cursor-not-allowed disabled:opacity-60']) }}>
    {{ $slot }}
</button>
