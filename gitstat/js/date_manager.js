let currentDate = new Date();
let currentMode = "year";

const formatDate = (date) => {
    date = new Date(date);
    const jour = String(date.getDate()).padStart(2, '0');
    const mois = String(date.getMonth() + 1).padStart(2, '0');
    const annee = date.getFullYear();
    return `${jour}/${mois}/${annee}`;
};

const formatMois = (date) => {
    date = new Date(date);
    const mois = String(date.getMonth() + 1).padStart(2, '0');
    const annee = date.getFullYear();
    return `${mois}/${annee}`;
};

const JourSemaine = (date) => {
    date = new Date(date);
    const jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    return jours[date.getDay()];
};

function getSemaine(date) {//calculer la semaine
    const Jour = date.getDay(); // 0 (Dimanche) à 6 (Samedi)
    const diffjour = (Jour === 0 ? -6 : 1) - Jour;

    const debut = new Date(date);
    debut.setDate(date.getDate() + diffjour);//calculer le début de la semaine

    const fin = new Date(debut);
    fin.setDate(debut.getDate() + 6);

    return {
        debut: debut.toISOString(),
        fin: fin.toISOString(),
    };

}

function getMois(year, month) {//calculer le mois
    const debut = new Date(year, month, 1);
    const fin = new Date(year, month + 1, 0);

    return {
        debut: debut.toISOString(),
        fin: fin.toISOString(),
    };
}

function Getannee(year) {// calculer l'année
    const debut = new Date(year, 0, 1);
    const fin = new Date(year, 11, 31);

    return {
        debut: debut.toISOString(),
        fin: fin.toISOString(),
    };
}

function update() {//mise à jour selon le mode
    const year = currentDate.getFullYear();
    let range = {};


    if (currentMode === "week") {
        range = getSemaine(currentDate);
    } else if (currentMode === "month") {
        range = getMois(year, currentDate.getMonth()); 
    } else if (currentMode === "year") {
        range = Getannee(year); 
    }

    fetchCommits(range.debut, range.fin,currentMode);
}

function changeDate(direction) {
    if (currentMode === "week") {
        currentDate.setDate(currentDate.getDate() + (direction === "next" ? 7 : -7));
    } else if (currentMode === "month") {
        currentDate.setMonth(currentDate.getMonth() + (direction === "next" ? 1 : -1));
    } else if (currentMode === "year") {
        currentDate.setFullYear(currentDate.getFullYear() + (direction === "next" ? 1 : -1));
    }

    update();
}

document.getElementById("prev").addEventListener("click", () => changeDate("prev"));
document.getElementById("next").addEventListener("click", () => changeDate("next"));

document.getElementById("semaine").addEventListener("click", () => {
    currentMode = "week";
    update();
});

document.getElementById("mois").addEventListener("click", () => {
    currentMode = "month";
    update();
});

document.getElementById("annee").addEventListener("click", () => {
    currentMode = "year";
    update();
});

