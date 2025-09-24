export default function formatNumber(value) {
    const number = Number(value);
    if (!value || isNaN(number)) return "0";
    return number.toLocaleString("en-US");
}
export function formatViews(num) {
    if (num < 1000) return num.toString();

    // Tính số nguyên nghìn
    const thousands = Math.floor(num / 1000);
    // Lấy phần dư để hiển thị sau chữ 'k'
    const remainder = Math.floor((num % 1000) / 100);

    if (num < 1_000_000) {
        // Hiển thị dạng 1k, 1k1, 1k2...
        return remainder > 0 ? `${thousands}k${remainder}` : `${thousands}k`;
    }

    // Tính số nguyên triệu
    const millions = Math.floor(num / 1_000_000);
    const remainderM = Math.floor((num % 1_000_000) / 100_000);

    // Hiển thị dạng 1M, 1M1...
    return remainderM > 0 ? `${millions}M${remainderM}` : `${millions}M`;
}
