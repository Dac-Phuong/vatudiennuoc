import { computed, reactive, onMounted } from "vue";

const LAYOUT_CONFIG_KEY = "vue-layout-config";
const LAYOUT_STATE_KEY = "vue-layout-state";

const layoutConfig = reactive({
    preset: "Aura",
    primary: "emerald",
    surface: null,
    darkTheme: false,
    menuMode: "static",
});

const layoutState = reactive({
    staticMenuDesktopInactive: false,
    overlayMenuActive: false,
    profileSidebarVisible: false,
    configSidebarVisible: false,
    staticMenuMobileActive: false,
    menuHoverActive: false,
    activeMenuItem: null,
});

// Lưu config vào localStorage
function saveLayoutConfig() {
    localStorage.setItem(LAYOUT_CONFIG_KEY, JSON.stringify(layoutConfig));
}

// Lưu state vào localStorage
function saveLayoutState() {
    localStorage.setItem(LAYOUT_STATE_KEY, JSON.stringify(layoutState));
}

// Load config từ localStorage
function loadLayoutConfig() {
    const savedConfig = localStorage.getItem(LAYOUT_CONFIG_KEY);
    if (savedConfig) {
        Object.assign(layoutConfig, JSON.parse(savedConfig));

        // Áp dụng dark theme nếu có
        if (layoutConfig.darkTheme) {
            document.documentElement.classList.add("app-dark");
        }
    }
}

// Load state từ localStorage
function loadLayoutState() {
    const savedState = localStorage.getItem(LAYOUT_STATE_KEY);
    if (savedState) {
        Object.assign(layoutState, JSON.parse(savedState));
    }
}

export function useLayout() {
    // Load saved state khi hook được khởi tạo
    onMounted(() => {
        loadLayoutConfig();
        loadLayoutState();
    });

    const setActiveMenuItem = (item) => {
        layoutState.activeMenuItem = item.value || item;
        saveLayoutState();
    };

    const toggleDarkMode = () => {
        if (!document.startViewTransition) {
            executeDarkModeToggle();
            return;
        }

        document.startViewTransition(() => executeDarkModeToggle(event));
    };

    const executeDarkModeToggle = () => {
        layoutConfig.darkTheme = !layoutConfig.darkTheme;
        document.documentElement.classList.toggle("app-dark");
        saveLayoutConfig();
    };

    const toggleMenu = () => {
        if (layoutConfig.menuMode === "overlay") {
            layoutState.overlayMenuActive = !layoutState.overlayMenuActive;
        }

        if (window.innerWidth > 991) {
            layoutState.staticMenuDesktopInactive =
                !layoutState.staticMenuDesktopInactive;
        } else {
            layoutState.staticMenuMobileActive =
                !layoutState.staticMenuMobileActive;
        }

        saveLayoutState();
    };

    const isSidebarActive = computed(
        () =>
            layoutState.overlayMenuActive || layoutState.staticMenuMobileActive
    );

    const isDarkTheme = computed(() => layoutConfig.darkTheme);

    const getPrimary = computed(() => layoutConfig.primary);

    const getSurface = computed(() => layoutConfig.surface);

    return {
        layoutConfig,
        layoutState,
        toggleMenu,
        isSidebarActive,
        isDarkTheme,
        getPrimary,
        getSurface,
        setActiveMenuItem,
        toggleDarkMode,
    };
}
