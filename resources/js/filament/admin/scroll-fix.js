document.addEventListener("livewire:navigated", function () {
    setTimeout(() => {
        let sidebar_item = document.querySelector(".fi-sidebar-item-active");
        if (sidebar_item)
            sidebar_item.scrollIntoView({
                block: "center",
                inline: "center",
                behavior: "smooth",
            });
    }, 300);
});
