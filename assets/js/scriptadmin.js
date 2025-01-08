
            function ouvrirModalTag() {
                document.getElementById('modalTag').classList.remove('hidden');
            }

            function fermerModalTag() {
                document.getElementById('modalTag').classList.add('hidden');
            }

            function ouvrirModalModifierTag(id, nom) {
                document.getElementById('modifierTagId').value = id;
                document.getElementById('modifierNom').value = nom;
                document.getElementById('modalModifierTag').classList.remove('hidden');
            }

            function fermerModalModifierTag() {
                document.getElementById('modalModifierTag').classList.add('hidden');
            }
