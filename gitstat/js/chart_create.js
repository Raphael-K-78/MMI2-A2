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

const GroupeCommitStats = (commits_stat, par) => {
    const groupe = {};
    // console.log("donnée envoyer à la fonction:",commits_stat);
    commits_stat.forEach(commit => {
        // console.log("Traitement du commit:", commit); // Vérifiez les données du commit
        const commitDate = commit.date.split('T')[0]; // Formate la date en "YYYY-MM-DD"
        // console.log("Date formatée:", commitDate);

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

        // console.log("Clé de regroupement:", groupKey); // Vérifiez la clé utilisée pour regrouper

        if (!groupe[groupKey]) {
            groupe[groupKey] = { filesChanged: 0, insertions: 0, deletions: 0 };
        }

        // Accumule les statistiques pour chaque période
        groupe[groupKey].filesChanged += commit.filesChanged;
        groupe[groupKey].insertions += commit.insertions;
        groupe[groupKey].deletions += commit.deletions;
    });

    // console.log("Résultat après regroupement:", groupe); // Vérifiez le résultat final

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
            // console.log("Commits entre", debut, "et", fin, ":");
            // console.log(commits);
            
            const commitsTrier = GroupeCommits(commits, par);
            
            var labels = Object.keys(commitsTrier); // Les clés sont les dates (ou jours/semaines/mois)
            var values = Object.values(commitsTrier); // Nombre de commits pour chaque groupe
            var data = values.map((value, index) => ({
                x: labels[index],  
                y: value,         
                r: value*3,
            }));
            var backgroundColors = [];
            var borderColors = [];

            labels.forEach(() => {
                const color = getRandomColor();
                backgroundColors.push(`rgba(${color}, 0.3)`);  // Couleur avec 30% d'opacité
                borderColors.push(`rgba(${color}, 1)`);        // Couleur avec opacité complète
            });        
            // console.log(backgroundColors);
            // console.log(backgroundColors);
            var title =`Commits de ${SelectedUser} pour le repo ${SelectedRepo} entre ${formatDate(debut)} et ${formatDate(fin)} (Groupé par ${par})`;

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
            const fetchCommitsStat = async () => {
                const commits_stat = [];
            
                // Utilisation de `for...of` pour itérer de manière asynchrone
                for (const commit of commits) {
                    try {
                        // Attendre la réponse de la requête fetch
                        const response = await fetch(`https://api.github.com/repos/${SelectedUser}/${SelectedRepo}/commits/${commit.sha}`, {
                            headers: accessToken ? { Authorization: `token ${accessToken}` } : {}
                        });
            
                        if (!response.ok) {
                            throw new Error(`Erreur lors de la récupération des données du commit: ${response.statusText}`);
                        }
            
                        // Récupérer les données du commit
                        const data = await response.json();
            
                        const filesChanged = data.files.length;
                        const insertions = data.stats.additions;
                        const deletions = data.stats.deletions;
            
                        // Ajouter les informations du commit dans `commits_stat`
                        commits_stat.push({
                            sha: commit.sha,
                            filesChanged,
                            insertions,
                            deletions,
                            date: data.commit.author.date
                        });
                    } catch (error) {
                        console.error("Erreur lors de la récupération du commit:", error);
                    }
                }
            
                // Une fois les données récupérées, vous pouvez traiter `commits_stat`
                // console.log("commits_stat après récupération des données:", commits_stat);
            
                // Trier les données
            const commits_statTrier = GroupeCommitStats(commits_stat, par);
                // console.log("commits_statTrier:", commits_statTrier);
            
                const labels = Object.keys(commits_statTrier); 
                const valuesFilesChanged = Object.values(commits_statTrier).map((group, index) => ({
                    x: labels[index], 
                    y: group.filesChanged 
                }));
                
                const valuesInsertions = Object.values(commits_statTrier).map((group, index) => ({
                    x: labels[index], 
                    y: group.insertions 
                }));
                
                const valuesDeletions = Object.values(commits_statTrier).map((group, index) => ({
                    x: labels[index], 
                    y: group.deletions 
                }));
            console.log("Labels:", labels);
            console.log("Files Changed:", valuesFilesChanged);
            console.log("Insertions:", valuesInsertions);
            console.log("Deletions:", valuesDeletions);

            // Vérifiez si le graphique existe déjà
            if (window.Chart3) {
                window.Chart2.data.labels = []; // Réinitialiser les anciens labels
                window.Chart3.data.labels = labels; 

                // Mettre à jour les datasets avec les nouvelles valeurs
                window.Chart3.data.datasets[0].data = valuesDeletions; // Mettre à jour les deletions
                window.Chart3.data.datasets[1].data = valuesInsertions; // Mettre à jour les insertions
                window.Chart3.data.datasets[2].data = valuesFilesChanged; // Mettre à jour les filesChanged

                // Mettre à jour le titre du graphique
                window.Chart3.options.plugins.title.text = `Commits de ${SelectedUser} pour le repo ${SelectedRepo} entre ${formatDate(debut)} et ${formatDate(fin)} (Groupé par ${par})`;

                // Mettre à jour le graphique
                window.Chart3.update();
            }
            else {                
                // Si le graphique n'existe pas, créer un nouveau graphique
                window.Chart3 = new Chart(ctx_commit, {
                    type: 'line', // Type de graphique : ligne
                    data: {
                        labels: labels, // Les labels sur l'axe des X (par exemple, mois ou périodes)
                        datasets: [
                            {
                                label: 'Deletions', 
                                data: valuesDeletions,
                                borderColor: 'red', 
                                backgroundColor: 'rgba(255, 0, 0, 0.1)', 
                                borderWidth: 2,
                                fill: false,
                                tension: 0.4,
                            },
                            {
                                label: 'Insertions', 
                                data: valuesInsertions, 
                                borderColor: 'green', 
                                backgroundColor: 'rgba(0, 255, 0, 0.1)', 
                                borderWidth: 2,
                                fill: false, 
                                tension: 0.4,
                            },
                            {
                                label: 'Files Changed',
                                data: valuesFilesChanged, 
                                borderColor: 'blue', 
                                backgroundColor: 'rgba(0, 0, 255, 0.1)', 
                                borderWidth: 2,
                                fill: false, 
                                tension: 0.4, 
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        // Affichage personnalisé du tooltip pour chaque ligne
                                        return `${context.dataset.label}: ${context.raw.y}`;
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: `Commits de ${SelectedUser} pour le repo ${SelectedRepo} entre ${formatDate(debut)} et ${formatDate(fin)} (Groupé par ${par})`,
                                color: 'white',
                                font: {
                                    size: 18,
                                    weight: 'bold'
                                }
                            },
                            legend: {
                                display: true, // Affichage de la légende
                                labels: {
                                    color: 'white', // Couleur de la légende
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'category', // Type de l'axe X : 'category' pour des périodes comme mois ou année
                                labels: labels, // Les labels sur l'axe des X (mois, années, etc.)
                                title: {
                                    display: true,
                                    text: 'Périodes',
                                    color: 'white'
                                },
                                ticks: {
                                    color: 'white', // Couleur des ticks de l'axe X
                                },
                                grid: {
                                    color: 'white',
                                    opacity: 0.5,
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    suggestedMin: 0,  // Minimum suggéré
                                    suggestedMax: 100, // Maximum suggéré
                                    stepSize: 10,      // Ajuster stepSize en conséquence
                                    color: 'white',
                                },
                                title: {
                                    display: true,
                                    text: 'Valeur des Commits',
                                    color: 'white'
                                },
                                grid: {
                                    color: 'white',
                                    opacity: 0.5,
                                }
                            }
                        }
                    }
                });
            }

                
            
            
                // Vous pouvez ensuite utiliser ces données pour afficher un graphique, etc.
            };
            
            // Appel de la fonction asynchrone
            fetchCommitsStat();
            
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
        
        // Convertir les valeurs en tableau pour traitement
const calc = Object.values(data);

// Calcul de la Moyenne
const moyenne = calc.reduce((sum, value) => sum + value, 0) / calc.length;

// Calcul de la Médiane
const sortedValues = calc.sort((a, b) => a - b);
let mediane;
if (sortedValues.length % 2 === 0) {
    mediane = (sortedValues[sortedValues.length / 2 - 1] + sortedValues[sortedValues.length / 2]) / 2;
} else {
    mediane = sortedValues[Math.floor(sortedValues.length / 2)];
}

// Calcul de l'Écart-type
const variance = calc.reduce((variance, value) => variance + Math.pow(value - moyenne, 2), 0) / calc.length;
const ecartType = Math.sqrt(variance);

// Insertion des résultats dans la div #math
const mathDiv = document.getElementById('math');
mathDiv.innerHTML = `
    <p><strong>Moyenne des langages utilisés en octets : </strong>${moyenne.toFixed(2)} octets</p>
    <p><strong>Médiane des langages utilisés en octets : </strong>${mediane.toFixed(2)} octets</p>
    <p><strong>Écart-type des langages utilisés en octets : </strong>${ecartType.toFixed(2)} octets</p>
`;
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
