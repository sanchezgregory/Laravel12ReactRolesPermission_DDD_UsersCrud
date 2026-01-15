

export default function AppLogo() {
    return (
        <>
            <div className="flex items-center gap-3 group cursor-pointer">
                <div className="relative w-10 h-10 flex items-center justify-center">
                    <div className="absolute inset-0 bg-gradient-to-tr from-orange-500 to-red-600 rounded-xl rotate-3 group-hover:rotate-6 transition-transform duration-300 shadow-lg shadow-orange-500/30"></div>
                    <div className="relative z-10 text-white font-black text-xl tracking-tighter">W</div>
                </div>
                <span className="text-2xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700 dark:from-white dark:to-zinc-400">WHAP</span>
            </div>
        </>
    );
}
