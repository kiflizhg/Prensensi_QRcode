(() => {
    const navigation = document.querySelector('.admin-navigation');
    if (!navigation) return;
    const desktop = window.matchMedia('(min-width: 981px)');
    const sync = () => { navigation.open = desktop.matches; };
    sync();
    desktop.addEventListener('change', sync);
    navigation.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !desktop.matches) {
            navigation.open = false;
            navigation.querySelector('summary').focus();
        }
    });
})();
