import { createInertiaApp } from "@inertiajs/react";
import { createRoot } from "react-dom/client";

if (typeof window === "undefined") {
    // evita execução no SSR do Vite
    throw new Error("SSR disabled");
}

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob("./Pages/**/*.tsx", { eager: true });
        return pages[`./Pages/${name}.tsx`];
    },

    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});
