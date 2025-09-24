function slugify(text) {
    return text
        .toString()
        .toLowerCase()
        .normalize("NFD") // tách dấu ra
        .replace(/[\u0300-\u036f]/g, "") // xóa dấu
        .replace(/đ/g, "d") // xử lý riêng chữ đ
        .replace(/[^a-z0-9\s-]/g, "")
        .trim()
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-");
}

export function generateSlug(title) {
    return slugify(title);
}
