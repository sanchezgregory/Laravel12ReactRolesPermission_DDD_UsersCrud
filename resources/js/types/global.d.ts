/// <reference types="react" />
/// <reference types="react-dom" />

declare module '@/Layouts/*' {
    const component: React.ComponentType<any>;
    export default component;
}

declare module '@/Components/*' {
    const component: React.ComponentType<any>;
    export default component;
}

declare module '@/Pages/*' {
    const component: React.ComponentType<any>;
    export default component;
}