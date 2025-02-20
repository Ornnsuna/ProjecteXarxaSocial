const cardImages = [
  "./../img/reverseMTG.png",
  "./../img/reversePKMN.png",
  "./../img/reverseOP.png",
  "./../img/reverseYu.png"
];
const createCard = () => {
  const card = document.createElement("div");
  card.className = "falling-card";
  card.style.setProperty("--start-x", Math.random());
  card.style.setProperty("--end-x", Math.random());
  card.style.animationDuration = `${Math.random() * 5 + 5}s`;
  card.style.animationDelay = `${Math.random() * 2}s`;
  card.style.backgroundImage = `url(${cardImages[Math.floor(Math.random() * cardImages.length)]})`;
  document.body.appendChild(card);

  card.addEventListener("animationend", () => {
    card.remove();
  });
};

setInterval(createCard, 1000);