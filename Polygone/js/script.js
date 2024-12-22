const video = document.getElementById("Video");
const fade = document.getElementById("fade");
const elementClick = {};
let vdo;
const youtubePlayers = {}; //cache pour l'API YTB

function onYouTubeIframeAPIReady() {
    console.log("API YouTube chargée");
  }


const click = (event) => {
    const clickedElement  = event.currentTarget;//element actuel
    const id = clickedElement.getAttribute('id');//recup id

    if (!elementClick[id]) {
        elementClick[id] = true;
        // console.log(elementClick);
    }
    const Count = Object.keys(elementClick).length;//taille de la var
    document.getElementById("count").innerHTML = Count;//affichage du compteur
    // console.log(Count);
}


const closed = (id) => {
  const dialog = document.getElementById(id);
  dialog.style.transform = "scale(0.1)";
  
  setTimeout(() => {
    dialog.close();
    const vdoId = id + "-vdo";
    const vdo = document.getElementById(vdoId);
    if (vdo) {
    //   console.log(`${vdoId}`);
      if (youtubePlayers[vdoId]) {//check cache
        youtubePlayers[vdoId].pauseVideo();
      } else {
        // Sinon, initialisez un nouveau lecteur
        youtubePlayers[vdoId] = new YT.Player(vdoId, {
          events: {
            onReady: () => {
              youtubePlayers[vdoId].pauseVideo();
            },
          },
        });
      }
    }

    if (Object.keys(elementClick).length == 4) {
      setTimeout(() => {
        document.getElementById("stage_2").style.backgroundImage = "url('upload/final2.png')";
        document.getElementById('overlay').style.opacity = 0;
        document.getElementById('conclusion').style.transform = "scale(1.2)";
    }, 1000);
    }
  }, 1000);
};
video.addEventListener("click",()=>{//stage 1
    document.getElementById("text").style.transform = "scale(1.2)";
    video.play();
    document.getElementById("header_txt").style.transform = "scale(3)";
    document.getElementById("header_txt").style.opacity = "0";
    //son
})


video.addEventListener('ended',()=>{//fade stage 1
    fade.style.opacity = "1";
})

fade.addEventListener('click',()=>{// stage 1 => stage 2
    document.getElementById("stage_1").style.opacity = 0;
    setTimeout(()=>{
        document.getElementById("stage_1").style.display = "none";
        setTimeout(()=>{
            document.getElementById("stage_2").style.visibility = "visible";
            document.getElementById("lampe").play();
            document.getElementById("stage_mid").style.display = "none";
            //son

        })
    },2000);
})


document.getElementById("element1").addEventListener('click',(event)=>{
    document.getElementById("dialog1").showModal();//affiche la boite de dialog
    document.getElementById("dialog1").style.transform = "scale(1)";
    click(event);
});
document.getElementById("element2").addEventListener('click',(event)=>{
    document.getElementById("dialog2").showModal();//affiche la boite de dialog
    document.getElementById("dialog2").style.transform = "scale(1)";
    click(event);
});
document.getElementById("element3").addEventListener('click',(event)=>{
    document.getElementById("dialog3").showModal();//affiche la boite de dialog
    document.getElementById("dialog3").style.transform = "scale(1)";
    click(event);
});
document.getElementById("element4").addEventListener('click',(event)=>{
    document.getElementById("dialog4").showModal();//affiche la boite de dialog
    document.getElementById("dialog4").style.transform = "scale(1)";
    click(event);
});


//canva
const canvas = document.getElementById('overlay');
const ctx = canvas.getContext('2d');

canvas.width = window.innerWidth;
canvas.height = window.innerHeight;

let mouseX = -1;
let mouseY = -1;
const haloRadius = { normal: 100, hover: 130 };//taille du halo

document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX;
    mouseY = e.clientY;
});

window.addEventListener('resize', () => {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
});//changer la taille du canva si on change la taille

const areas = document.querySelectorAll('.clickable');
let isHovering = false;// on est sur quelquechose ?

areas.forEach((area) => {//vérifie si on est en hover
    area.addEventListener('mouseenter', () => {
        isHovering = true;
    });
    area.addEventListener('mouseleave', () => {
        isHovering = false;
    });
});

const drawOverlay = ()=>{//dessine 
    ctx.filter = 'blur(20px)';//ajout effect
    ctx.fillStyle = 'rgba(0, 0, 0, 1)';//couleur
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    if (mouseX >= 0 && mouseY >= 0) {
        const radius = isHovering ? haloRadius.hover : haloRadius.normal;
        ctx.save();
        ctx.globalCompositeOperation = 'destination-out';
        ctx.beginPath();
        ctx.arc(mouseX, mouseY, radius, 0, Math.PI * 2);
        ctx.fill();
        ctx.filter='none';
        ctx.restore();
    }
    requestAnimationFrame(drawOverlay);
}
drawOverlay();
