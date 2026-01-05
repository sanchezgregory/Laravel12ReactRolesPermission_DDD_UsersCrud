import { type SharedData } from '@/types';
import { Head, Link, usePage } from '@inertiajs/react';

export default function Welcome() {
    const { auth } = usePage<SharedData>().props;

    return (
        <>
            <Head title="Whap - Servicios de Mediación">
                <link rel="preconnect" href="https://fonts.bunny.net" />
                <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
            </Head>

            <div className="min-h-screen bg-white dark:bg-black font-sans text-slate-900 dark:text-zinc-100 selection:bg-orange-500 selection:text-white overflow-hidden relative" style={{ fontFamily: "'Instrument Sans', sans-serif" }}>

                {/* Background Decor */}
                <div className="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
                    <div className="absolute -top-[10%] -left-[10%] w-[50%] h-[50%] bg-gradient-to-br from-orange-400/20 to-red-500/20 rounded-full blur-3xl opacity-60 mix-blend-multiply dark:mix-blend-screen animate-blob"></div>
                    <div className="absolute top-[20%] -right-[10%] w-[40%] h-[40%] bg-gradient-to-bl from-purple-400/20 to-indigo-500/20 rounded-full blur-3xl opacity-60 mix-blend-multiply dark:mix-blend-screen animate-blob animation-delay-2000"></div>
                    <div className="absolute -bottom-[10%] left-[20%] w-[60%] h-[60%] bg-gradient-to-t from-pink-400/20 to-rose-500/20 rounded-full blur-3xl opacity-60 mix-blend-multiply dark:mix-blend-screen animate-blob animation-delay-4000"></div>
                </div>

                {/* Navbar */}
                <header className="fixed top-0 w-full z-50 transition-all duration-300">
                    <div className="max-w-7xl mx-auto px-6 h-24 flex items-center justify-between">
                        {/* Logo */}
                        <div className="flex items-center gap-3 group cursor-pointer">
                            <div className="relative w-10 h-10 flex items-center justify-center">
                                <div className="absolute inset-0 bg-gradient-to-tr from-orange-500 to-red-600 rounded-xl rotate-3 group-hover:rotate-6 transition-transform duration-300 shadow-lg shadow-orange-500/30"></div>
                                <div className="relative z-10 text-white font-black text-xl tracking-tighter">W</div>
                            </div>
                            <span className="text-2xl font-bold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-slate-900 to-slate-700 dark:from-white dark:to-zinc-400">Whap</span>
                        </div>

                        {/* Navigation */}
                        <nav className="flex items-center gap-6">
                            {auth.user ? (
                                <Link
                                    href={route('mediators.index')}
                                    className="hidden sm:inline-flex items-center justify-center rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-6 py-2.5 text-sm font-semibold shadow-lg shadow-slate-900/20 hover:scale-105 hover:shadow-xl transition-all duration-300"
                                >
                                    Ver mediadores
                                </Link>
                            ) : (
                                <>
                                    <Link
                                        href={route('backoffice.login')}
                                        className="hidden sm:block text-sm font-semibold text-slate-600 hover:text-orange-600 dark:text-zinc-400 dark:hover:text-white transition-colors"
                                    >
                                        Iniciar sesión
                                    </Link>
                                    <Link
                                        href={route('mediators.index')}
                                        className="inline-flex items-center justify-center rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-6 py-2.5 text-sm font-semibold shadow-lg shadow-slate-900/20 hover:scale-105 hover:shadow-xl transition-all duration-300"
                                    >
                                        Ver mediadores
                                    </Link>
                                </>
                            )}
                        </nav>
                    </div>
                </header>

                {/* Hero Section */}
                <main className="relative pt-32 pb-20 px-6 lg:pt-48 lg:pb-32 max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12 lg:gap-24">
                    {/* Text Content */}
                    <div className="flex-1 text-center lg:text-left z-10 space-y-8 max-w-2xl lg:max-w-none mx-auto">
                        <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300 text-xs font-bold tracking-wide uppercase mb-4 animate-fade-in-up">
                            <span className="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                            Servicios Profesionales
                        </div>

                        <h1 className="text-5xl lg:text-7xl font-extrabold tracking-tight leading-[1.1] animate-fade-in-up animation-delay-100">
                            Resolución de conflictos <br />
                            <span className="text-transparent bg-clip-text bg-gradient-to-r from-orange-500 via-red-500 to-purple-600">
                                simplificada.
                            </span>
                        </h1>

                        <p className="text-xl text-slate-600 dark:text-zinc-400 leading-relaxed animate-fade-in-up animation-delay-200">
                            Whap ofrece servicios de mediación para empresas y personas con costos asequibles. Conectamos partes para construir soluciones duraderas.
                        </p>

                        <div className="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4 animate-fade-in-up animation-delay-300">
                            <Link
                                href={route('mediators.index')}
                                className="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-full bg-gradient-to-r from-orange-600 to-red-600 text-white px-8 py-4 text-lg font-bold shadow-lg shadow-orange-500/30 hover:shadow-orange-500/50 hover:-translate-y-1 transition-all duration-300"
                            >
                                Ver mediadores
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </Link>

                            {!auth.user && (
                                <Link
                                    href={route('backoffice.register')}
                                    className="w-full sm:w-auto inline-flex justify-center items-center rounded-full bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 text-slate-900 dark:text-white px-8 py-4 text-lg font-semibold hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors"
                                >
                                    Registrarse
                                </Link>
                            )}
                        </div>
                    </div>

                    {/* Abstract Visuals */}
                    <div className="flex-1 w-full max-w-lg lg:max-w-none relative animate-fade-in-up animation-delay-500 hidden md:block">
                        <div className="relative aspect-square">
                            {/* Card Stack Effect */}
                            <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[80%] h-[80%] bg-gradient-to-br from-slate-100 to-white dark:from-zinc-900 dark:to-zinc-800 rounded-3xl rotate-6 shadow-2xl border border-white/50 dark:border-white/5 opacity-40 backdrop-blur-sm z-0"></div>
                            <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[85%] h-[85%] bg-gradient-to-br from-slate-100 to-white dark:from-zinc-900 dark:to-zinc-800 rounded-3xl -rotate-3 shadow-2xl border border-white/50 dark:border-white/5 opacity-70 backdrop-blur-sm z-10"></div>

                            {/* Main Card */}
                            <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-white dark:bg-zinc-950 rounded-3xl shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] dark:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.5)] border border-slate-100 dark:border-zinc-800 overflow-hidden z-20 flex flex-col">
                                {/* Fake UI Header */}
                                <div className="px-6 py-4 border-b border-slate-100 dark:border-zinc-900 flex items-center justify-between bg-slate-50/50 dark:bg-zinc-900/50">
                                    <div className="flex gap-2">
                                        <div className="w-3 h-3 rounded-full bg-red-400"></div>
                                        <div className="w-3 h-3 rounded-full bg-yellow-400"></div>
                                        <div className="w-3 h-3 rounded-full bg-green-400"></div>
                                    </div>
                                    <div className="h-2 w-20 bg-slate-200 dark:bg-zinc-800 rounded-full"></div>
                                </div>

                                {/* Fake Content */}
                                <div className="p-8 flex-1 flex flex-col gap-6 relative">
                                    <div className="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] opacity-20"></div>

                                    <div className="flex items-center gap-4">
                                        <div className="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/40 flex items-center justify-center text-orange-600">
                                            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        </div>
                                        <div>
                                            <div className="h-4 w-32 bg-slate-200 dark:bg-zinc-800 rounded mb-2"></div>
                                            <div className="h-3 w-20 bg-slate-100 dark:bg-zinc-800/50 rounded"></div>
                                        </div>
                                    </div>

                                    <div className="space-y-3">
                                        <div className="h-3 w-full bg-slate-100 dark:bg-zinc-900 rounded"></div>
                                        <div className="h-3 w-5/6 bg-slate-100 dark:bg-zinc-900 rounded"></div>
                                        <div className="h-3 w-4/6 bg-slate-100 dark:bg-zinc-900 rounded"></div>
                                    </div>

                                    <div className="mt-auto flex gap-3">
                                        <div className="flex-1 h-10 bg-slate-900 dark:bg-white rounded-lg opacity-10"></div>
                                        <div className="flex-1 h-10 bg-orange-500 rounded-lg opacity-20"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                {/* Footer Decor */}
                <div className="absolute bottom-0 w-full h-px bg-gradient-to-r from-transparent via-slate-200 dark:via-zinc-800 to-transparent"></div>
            </div>

            <style>{`
                @keyframes blob {
                    0% { transform: translate(0px, 0px) scale(1); }
                    33% { transform: translate(30px, -50px) scale(1.1); }
                    66% { transform: translate(-20px, 20px) scale(0.9); }
                    100% { transform: translate(0px, 0px) scale(1); }
                }
                .animate-blob {
                    animation: blob 10s infinite;
                }
                .animation-delay-2000 {
                    animation-delay: 2s;
                }
                .animation-delay-4000 {
                    animation-delay: 4s;
                }
                .animate-fade-in-up {
                    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                    opacity: 0;
                    transform: translateY(20px);
                }
                .animation-delay-100 { animation-delay: 0.1s; }
                .animation-delay-200 { animation-delay: 0.2s; }
                .animation-delay-300 { animation-delay: 0.3s; }
                .animation-delay-500 { animation-delay: 0.5s; }
                
                @keyframes fadeInUp {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `}</style>
        </>
    );
}
