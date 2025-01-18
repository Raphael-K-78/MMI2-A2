const getRandomColor= ()=> {
    const r = Math.floor(Math.random() * 256); // red
    const g = Math.floor(Math.random() * 256); // Green
    const b = Math.floor(Math.random() * 256); // blue
    return `${r}, ${g}, ${b}`; //la suite des valeurs
}

const GroupeCommits = (commits, par) => {
    const groupe = {};

    commits.forEach(commit => {
        // console.log(commit.commit.author.date);
        const commitDate = commit.commit.author.date.split('T')[0]; // Format "YYYY-MM-DD"
        let groupKey;

        // Regroupement par semaine, mois ou année
        if (par === "week") {
            const jours = JourSemaine(commitDate); // Jour de la semaine (Lundi, Mardi, etc.)
            groupKey = jours;
        } else if (par === "month") {
            groupKey = formatDate(commitDate); // Format "DD/MM/YYYY"
        } else if (par === "year") {
            groupKey = formatMois(commitDate); // Format "MM/YYYY"
        }

        if (!groupe[groupKey]) {
            groupe[groupKey] = 0;
        }
        groupe[groupKey] += 1; // Incrémenter le nombre de commits pour ce groupe
    });

    return groupe;
};

function fetchCommits(debut, fin, par) {
    // repos_data
    // user_data
    const SelectedUser = $("#user_data").val();
    const SelectedRepo = $("#repos_data").val();

    fetch(`https://api.github.com/repos/${SelectedUser}/${SelectedRepo}/commits?since=${debut}&until=${fin}`,{
        headers: accessToken ? { Authorization: `token ${accessToken}` } : {}
    })
        .then(response => response.json())
        .then(commits => {
            // console.log("Commits entre", debut, "and", fin, ":");
            // console.log(commits);
            
            const commitsTrier = GroupeCommits(commits, par);

            const labels = Object.keys(commitsTrier); // Les clés sont les dates (ou jours/semaines/mois)
            const values = Object.values(commitsTrier); // Nombre de commits pour chaque groupe
            const data = values.map((value, index) => ({
                x: labels[index],  
                y: value,         
                r: value*3,
            }));
            const backgroundColors = [];
            const borderColors = [];

            labels.forEach(() => {
                const color = getRandomColor();
                backgroundColors.push(`rgba(${color}, 0.3)`);  // Couleur avec 30% d'opacité
                borderColors.push(`rgba(${color}, 1)`);        // Couleur avec opacité complète
            });        
            // console.log(backgroundColors);
            // console.log(backgroundColors);
            const title =`Commits de ${SelectedUser} pour le repo ${SelectedRepo} entre ${formatDate(debut)} et ${formatDate(fin)} (Groupé par ${par})`;

            // Si le graphique existe déjà, on met à jour ses données
            if (window.Chart2) {
                window.Chart2.data.labels = []; // Réinitialiser les anciens labels
                window.Chart2.options.scales.x.labels = labels;
                window.Chart2.data.datasets[0].data = data;
                window.Chart2.data.datasets[0].backgroundColor = backgroundColors;
                window.Chart2.data.datasets[0].borderColor = borderColors, // Couleur de la bordure
                window.Chart2.options.plugins.title.text = title;    
                window.Chart2.options.plugins.title.text = `Commits de ${SelectedUser} pour le repo ${SelectedRepo} entre ${formatDate(debut)} et ${formatDate(fin)} (Groupé par ${par})`;
                window.Chart2.update();
            } else {
                // Créer le graphique si il existe pas
                window.Chart2 = new Chart(ctx_time, {
                    type: 'bubble',
                    data: {
                        datasets: [{
                            label: 'Nombre de commits',
                            data: data,
                            borderWidth: 1,
                            backgroundColor:backgroundColors,
                            borderColor:borderColors,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                      return `Nombre de commits: ${context.raw.y}`;
                                    }
                              },
                            },
                            title: {
                                display: true,
                                text: title,
                                color: 'white',
                                font: {
                                    size: 18,
                                    weight: 'bold'
                                }
                            },
                            legend: {
                                display: false // Masquer la légende
                            }
                        },
                        scales: {
                            x: {
                              type: 'category',
                              labels: data.map(point => point.x),
                              title: {
                                display: false,
                              },
                              ticks: {
                                color: 'white',
                              },
                              grid: {
                                color: 'white',
                                opacity: 0.5,
                              },
                            },
                            y: {
                              beginAtZero: true,
                              ticks: {
                                beginAtZero: true,
                                stepSize: 1,
                                color: 'white',
                              },
                              title: {
                                display: true,
                                text: 'Nombre de commits',
                                color: 'white',
                              },
                              grid: {
                                color: 'white',
                                opacity: 0.5,
                              },
                            },
                          }
                    }
                });
            }
        })
        .catch(error => console.error("Erreur de récupération des commits:", error));
}



//define canvas
const ctx_language = document.getElementById('language_chart');
const ctx_time = document.getElementById('time_chart');
const ctx_commit = document.getElementById('commit_stat_chart');

$('#repos_data').on('select2:select', function (e) {
    document.getElementById('data').style.display = 'block';
    const SelectedRepo = e.params.data.id;
    const SelectedUser = $("#user_data").val();
    console.log(`Utilisateur sélectionné: ${SelectedUser}`);
    console.log(`Dépôt sélectionné: ${SelectedRepo}`);
    fetch(`https://api.github.com/repos/${SelectedUser}/${SelectedRepo}/languages`,{
        headers: accessToken ? { Authorization: `token ${accessToken}` } : {}
    })
    .then(response => response.json())
    .then(data => {
        console.log("Données des langages récupérées :", data);

        // Préparer les données pour le graphique
        const labels = Object.keys(data); // Langages
        const values = Object.values(data); // Quantité en octets
        const total = values.reduce((acc, value) => acc + value, 0); //Somme total des valeurs
        const percentages = values.map(value => ((value / total) * 100).toFixed(2)); // 2 décimales
        var title = `Langages de programmation utiliser par ${SelectedUser} pour le Repo ${SelectedRepo}`;
        const backgroundColors = [];
        const borderColors = [];

        labels.forEach(() => {
            const color = getRandomColor();
            backgroundColors.push(`rgba(${color}, 0.3)`);  // Couleur avec 30% d'opacité
            borderColors.push(`rgba(${color}, 1)`);         // Couleur avec opacité complète
        });        
        if (window.Chart1) {
            // Met à jour si le graphique existe déjà
            window.Chart1.data.labels = labels;
            window.Chart1.data.datasets[0].data = percentages;
            window.Chart1.data.datasets[0].backgroundColor = backgroundColors;
            window.Chart1.data.datasets[0].borderColor = borderColors, // Couleur de la bordure
            window.Chart1.options.plugins.title.text = title;
            window.Chart1.update();
        } else {
            // Crée un nouveau graphique si inexistant
            window.Chart1 = new Chart(ctx_language, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: percentages,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors, // Couleur de la bordure
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        tooltip: {
                            callbacks: {
                              label: function(context) {
                                return `${context.raw} %`; // Ajoute une unité
                              }
                            }
                          },
                        title: {
                            display: true,
                            text: title,
                            color: 'white', // Titre en blanc
                            font: {
                                size: 20, // Taille du texte
                                weight: 'bold' // Poids du texte (gras)
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: 'white', // Couleur des légendes en blanc
                                font: {
                                    size: 14, // Taille de la légende
                                }
                            }
                        }
                    },
                    // Style général du graphique
                    elements: {
                        arc: {
                            borderWidth: 1
                        }
                    },
                    layout: {
                        padding: 10
                    },
                    scales: {
                        x:{
                            display:false,
                            ticks: {
                                color: 'white' 
                              },
                              grid: {
                                color: 'white' 
                              },
                        },
                        y: {
                            beginAtZero: true, 
                            ticks: {
                                ticks: {
                                    color: 'white' 
                                  },
                                  grid: {
                                    color: 'white' 
                                  },
                                callback: function(value, index, ticks) {
                                  return value + ' %'; // Ajoute une unité
                                },
                            },
                            display:false,

                        },
                    },
                    backgroundColor: 'black', // Applique le fond noir au graphique
                },
            });
        }
    })
    currentDate = new Date();
    currentMode = "year";
    update();
});
