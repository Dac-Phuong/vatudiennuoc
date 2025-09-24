import { useToast } from "primevue/usetoast";

export default function useNotify() {
    const toast = useToast();

    const success = (message, summary = "Thông báo") => {
        toast.add({
            severity: "success",
            summary,
            detail: message,
            life: 3000,
        });
    };

    const error = (message, summary = "Thông báo") => {
        toast.add({ severity: "error", summary, detail: message, life: 3000 });
    };

    const warn = (message, summary = "Thông báo") => {
        toast.add({ severity: "warn", summary, detail: message, life: 3000 });
    };

    return { success, error, warn };
}
