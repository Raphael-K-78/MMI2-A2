let noneData = `
    <div id="none">
        <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
        Aucune donnée trouvée, modifiez les filtres.
        <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
    </div>
`;


function dialog_on(data) {
    if (data.success) {
        $('#nom').text(data.nom);
        $('#prenom').text(data.prenom);
        $('#age').text(data.age);
        $('#sexe').text(data.sexe); 
        $('#status').text(data.status);
        $("#background_img").attr("style","background-image:url('img/member/member_n"+ data.id+ ".png');")
        $('#tel_num').attr("href", "tel:"+data.phone);
        document.getElementById("member_desc").showModal();
        generateStars(data.avis,"#member_desc .star");
        // console.table(data);
        // console.log(data.phone);
        // console.log("background-image:url('img/member/member_n"+ data.id+ ".png');")
    }
}

function applyFilters() {
    let filters = {};

    // Parcourir chaque groupe de filtres
    $('#filter .filter-group').each(function () {
        let groupName = $(this).find('input').attr('name'); // Récupérer le nom du groupe
        let selectedValues = $(this).find('input:checked').map(function () {
            return $(this).val();
        }).get();

        if (selectedValues.length > 0) {
            filters[groupName] = selectedValues;
        }
    });

    // Préparer la requête
    let filterString = '';
    for (let group in filters) {
        if (filters[group].length > 0) {
            let groupConditions = '';
    
            if (group === 'Age') { // Exception pour l'âge
                for (let value of filters[group]) {
                    let [min, max] = value.split('-'); // Décompose "36-50" en 36 et 50
                    if (max === '+') { // Gère les cas comme "51+"
                        groupConditions += `(Age >= ${min}) OR `;
                    } else {
                        groupConditions += `(Age >= ${min} AND Age <= ${max}) OR `;
                    }
                }
            } else { // Conditions standards pour les autres groupes
                for (let value of filters[group]) {
                    groupConditions += `${group}='${value}' OR `;
                }
            }
    
            groupConditions = groupConditions.slice(0, -4); // Retirer le dernier " OR "
            filterString += `(${groupConditions}) AND `;
        }
    }
    filterString = filterString.slice(0, -5); // Retirer le dernier " AND "
    
    // console.log(filterString);
    article(filterString,null);
}

function generateStars(avis,container) {
    const starContainer = document.querySelector(container);
    starContainer.innerHTML = "";

    const limit = [1, 5, 10, 15, 20];
    const Stars = limit.length;

    for (let i = 0; i < Stars; i++) {
        const star = document.createElement('i'); 
        star.classList.add('fa', 'fa-star'); 
        if (avis >= limit[i]) {
            star.classList.add('star_2'); 
        }
        starContainer.appendChild(star);
    }
}

function article(filter,order){
    $.ajax({
        url: "php/fetch_members.php",
        method: "GET",
        dataType: "json",
        data: {
            order: order,
            filter:filter,
        },
        success: function (data) {
            document.getElementById("data").innerHTML = '';            
            // console.log(data.length);
            if(data.length === 0){
                $('#data').append(noneData);
            }
            
            data.forEach(member => {
                createArticle(member);
            });
        },
        error: function (data,xhr, status, error) {
            // console.log(data);
            console.log("Erreur AJAX :", error);
        }
    });
}

function createArticle(member) {
    const avis = member.MatchWin - member.MatchLoose;

    const article = $(`
        <article>
            <div id="article_n${member.ID}">
                <img src="img/member/member_n${member.ID}.png" alt="Photo de ${member.Nom}">
                <div>
                    <h3><a href="tel:${member.phone}"}><span>${member.Nom}</span>&nbsp;<span >${member.Prenom}</span></a></h3>
                    <p><span>${member.Status}</span>, &nbsp;<span>${member.Sexe}</span></p>
                    <p><span>${member.Age}</span> ans</p>
                    <div class="star"></div>
                </div>
            </div>
            <hr>
        </article>
    `);

    $('#data').append(article);
    const starContainer = "#article_n"+member.ID+" .star";
    // console.log(starContainer);
    // console.log(document.querySelector(starContainer));
    generateStars(avis, starContainer);

}

$(function () {
    article(null, null);
    $('.cta').on('click', function () {
        const scrollAmount = $(window).height();
        $('html, body').animate({
            scrollTop: $(window).scrollTop() + scrollAmount
        }, 2000, 'easeInOutQuad');
    });

    $('a[href^="#"]').on('click', function (event) {
        event.preventDefault();
        const target = $(this.getAttribute('href')); 
        $('html, body').animate({
            scrollTop: target.offset().top,
            },
                2000,
                'easeInOutQuad' 
            );
    });
    
    $('#membre').select2({
        lang: "fr",
        placeholder: "Rechercher un dormeur",
        allowClear: true
    });

    $('#membre').on('change', function () {
        var membreId = $(this).val();

        if (membreId) {
            $.ajax({
                url: "php/dialog_recup.php",
                type: "GET",
                data: { dialog: membreId },
                dataType: "json",
                success: function (data) {
                    // console.log("Données reçues :", data); // Vérifiez la structure des données
                    dialog_on(data);
                },
                error: function (data,xhr, status, error) {
                    console.log(data);
                    console.log("Erreur AJAX :", error);
                }
            });
        }
    });

    $(".generate").on('click', function () {
        article(null, null);
    });

    $('.filter-group input').on('change', applyFilters); 
});
