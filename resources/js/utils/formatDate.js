export default function formatDate(input) {
    if (!input) return "";

    const date = new Date(input);
    if (isNaN(date)) return "";

    const pad = (n) => n.toString().padStart(2, "0");

    const day = pad(date.getDate());
    const month = pad(date.getMonth() + 1);
    const year = date.getFullYear();

    const hours = pad(date.getHours());
    const minutes = pad(date.getMinutes());

    return `${hours}:${minutes} ngày ${day}/${month}/${year}`;
}
