document.addEventListener('DOMContentLoaded', () => {
    
    const gameBoard = document.getElementById('game-board');
    if (!gameBoard) return;

    // Vai buscar os dados injetados pelo PHP Vanilla
    const jwtToken = gameBoard.getAttribute('data-jwt'); 
    const roomId = gameBoard.getAttribute('data-room-id'); 
    const apiUrl = gameBoard.getAttribute('data-external-game-api-url') + roomId;

    // Mapeamento de naipes para símbolos e cores
    const suitSymbols = { 'H': '♥', 'D': '♦', 'S': '♠', 'C': '♣' };

    // Bloqueio para evitar múltiplos pedidos simultâneos de bots
    let botIsPlaying = false;

    function checkTokenValidity(response) {
        if (response.status === 401) {
            alert("A sua sessão (JWT) expirou. Vai ser redirecionado para o ecrã de Login.");
            // O redirecionamento para o /logout limpa a sessão PHP e volta à página inicial
            window.location.href = '/logout'; 
            return false;
        }
        return true;
    }

    async function restartGame() {
        try {
            const response = await fetch(`${apiUrl}/start`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + jwtToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!checkTokenValidity(response)) return;

            if (response.ok) {
                fetchGameState(); // Atualiza o estado do jogo após reiniciar
            } else {
                const result = await response.json();
                if (response.status !== 400) alert("Erro ao reiniciar o jogo: " + result.error);
            }
        } catch (error) {
            console.error("Erro ao reiniciar o jogo:", error);
        }
    }

    // [RF23] e [RF26] Consultar estado da partida
    async function fetchGameState() {
        if (!jwtToken) return;

        try {
            const response = await fetch(`${apiUrl}/state`, {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + jwtToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });


            if (!checkTokenValidity(response)) return;

            if (response.ok) {
                const data = await response.json();
                renderGame(data);
            } else {
                console.error("Erro da API. Status:", response.status);
            }
        } catch (error) {
            console.error("Erro ao buscar estado do jogo:", error);
        }
    }

    // [NOVO] Função automática para mandar a API processar a jogada do Bot
    async function forceBotPlay() {
        try {
            const response = await fetch(`${apiUrl}/play-bot`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + jwtToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!checkTokenValidity(response)) return;
            
            if (response.ok) {
                fetchGameState(); // Força a atualização da mesa logo após a jogada!
            } else {
                console.error("Erro ao forçar jogada do bot. Status:", response.status);
            }
        } catch (error) {
            console.error("Erro ao forçar jogada do bot:", error);
        } finally {
            botIsPlaying = false;
        }
    }

    // [RF24] Enviar carta jogada (Humano) para a API Laravel
    async function playCard(cardString) {
        try {
            const response = await fetch(`${apiUrl}/play`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + jwtToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ card: cardString })
            });
            
            const result = await response.json();

            if (!checkTokenValidity(response)) return;
            
            if (response.ok) {
                fetchGameState();
            } else {
                alert("Movimento Inválido: " + result.error);
            }
        } catch (error) {
            console.error("Erro ao jogar:", error);
        }
    }

    // Desenha o JSON recebido no HTML
    function renderGame(data) {
        // 1. Atualizar Pontuações
        document.getElementById('score-team-a').innerText = data.team_a_score;
        document.getElementById('score-team-b').innerText = data.team_b_score;

        const turnIndicator = document.getElementById('turn-indicator');
        
        // 2. Fim de Jogo (10 Vazas concluídas)
        if (data.trick_count === 10) {
            let msg = data.team_a_score > 60 ? "Vitória da Equipa A! 🏆" : (data.team_b_score > 60 ? "Vitória da Equipa B! 🏆" : "Empate! 🤝");
            turnIndicator.innerText = `FIM DE JOGO: ${msg}`;
            turnIndicator.className = "text-danger fs-5 mt-2 fw-bold bg-white p-1 rounded border border-danger";
            document.getElementById('my-hand-container').innerHTML = `
            <a href="/" class="btn btn-outline-light border-secondary mt-3 mb-3">Sair da Sala</a>
            <button id="btn-restart" class="btn btn-success fw-bold mt-3 mb-3 ms-2">Reiniciar Jogo</button>
            `;
            document.getElementById('btn-restart').onclick = restartGame;
        } else {
            // Trunfo e Turno normal
            if (data.trump_card) {
                const trumpSuit = data.trump_card.split('-')[0];
                const trumpRank = data.trump_card.split('-')[1];
                document.getElementById('trump-card').innerText = trumpRank + suitSymbols[trumpSuit];
            }
            
            // Lógica Inteligente para Deteção de Turno e Ação dos Bots
            if (data.current_player_id === data.my_id) {
                turnIndicator.innerText = "⭐ É A TUA VEZ DE JOGAR! ⭐";
                turnIndicator.className = "text-warning small fw-bold mt-1";
                botIsPlaying = false; // Reset de segurança
            } else if (data.current_player_id) {
                const currentPlayerInfo = data.players_info[data.current_player_id];
                
                // Se a vez for de um bot (identificado pelo Username)
                if (currentPlayerInfo && currentPlayerInfo.username.startsWith('Bot_')) {
                    turnIndicator.innerText = `A pensar... (${currentPlayerInfo.username})`;
                    turnIndicator.className = "text-info small fw-bold mt-1";
                    
                    // Se o bot ainda não estiver a jogar, inicia o processo de jogada automática
                    if (!botIsPlaying) {
                        botIsPlaying = true;
                        const thinkingTime = 1800 + Math.random() * 1200; // Entre 1.8s e 3s
                        setTimeout(forceBotPlay, thinkingTime);
                    }
                } else {
                    turnIndicator.innerText = `A aguardar jogada de ${currentPlayerInfo.username}...`;
                    turnIndicator.className = "text-white-50 small mt-1";
                    botIsPlaying = false;
                }
            }
        }

        // 3. Distribuição de Jogadores (Posicionamento Relativo à Mesa)
        const playerIds = Object.keys(data.players_info);
        const myIndex = playerIds.indexOf(String(data.my_id));

        if (myIndex !== -1) {
            const rightIndex = (myIndex + 1) % 4;
            const topIndex = (myIndex + 2) % 4;
            const leftIndex = (myIndex + 3) % 4;

            ['me', 'right', 'top', 'left'].forEach((pos, i) => {
                const idx = [myIndex, rightIndex, topIndex, leftIndex][i];
                const pId = playerIds[idx];
                
                document.getElementById(`name-${pos}`).innerText = data.players_info[pId].username;
                
                if(pos !== 'me') {
                    document.getElementById(`cards-${pos}`).innerText = `🂠 ${data.card_counts[pId]} cartas`;
                }
                
                const img = document.getElementById(`avatar-${pos}`);
                img.src = data.players_info[pId].avatar;
                img.classList.remove('d-none');
            });
        }

        // 4. Desenhar Cartas no Centro da Mesa (A Vaza atual)
        const tableContainer = document.getElementById('table-cards-container');
        tableContainer.innerHTML = ''; 
        
        if (data.table_cards && Object.keys(data.table_cards).length > 0) {
            Object.entries(data.table_cards).forEach(([pId, card]) => {
                
                // [ESCUDO] Se a carta não for texto válido, ignora para não encravar
                if (typeof card !== 'string' || !card.includes('-')) return;

                const suit = card.split('-')[0];
                const rank = card.split('-')[1];
                const cardDiv = document.createElement('div');
                
                let positionClass = 'table-card-0';
                if (pId === playerIds[(myIndex + 1) % 4]) positionClass = 'table-card-1';
                if (pId === playerIds[(myIndex + 2) % 4]) positionClass = 'table-card-2';
                if (pId === playerIds[(myIndex + 3) % 4]) positionClass = 'table-card-3';
                
                cardDiv.className = `playing-card suit-${suit} shadow border-dark position-absolute ${positionClass} played`;
                cardDiv.innerText = rank + suitSymbols[suit];
                cardDiv.title = `Carta jogada por: ${data.players_info[pId].username}`;
                
                tableContainer.appendChild(cardDiv);

                requestAnimationFrame(() => {
                    cardDiv.classList.add('table-show');
                    cardDiv.classList.remove('table-enter');
                });
            });
        } else {
            tableContainer.innerHTML = '<span class="text-white-50 small fst-italic">Mesa Vazia</span>';
        }

        // 5. Desenhar a Minha Mão
        if (data.trick_count < 10) {
            const handContainer = document.getElementById('my-hand-container');
            handContainer.innerHTML = ''; 
            
            if (data.my_hand && Array.isArray(data.my_hand)) {
                data.my_hand.forEach(card => {
                    
                    // [ESCUDO] Prevenção de lixo na mão
                    if (typeof card !== 'string' || !card.includes('-')) return;

                    const suit = card.split('-')[0];
                    const rank = card.split('-')[1];
                    const cardDiv = document.createElement('div');
                    cardDiv.className = `playing-card suit-${suit}`;
                    cardDiv.innerText = rank + suitSymbols[suit];
                    
                    if (data.current_player_id === data.my_id) {
                        cardDiv.classList.add('can-play');
                        cardDiv.onclick = () => playCard(card);
                    } else {
                        cardDiv.style.opacity = '0.5';
                        cardDiv.style.cursor = 'not-allowed';
                        cardDiv.style.transform = 'none';
                    }
                    handContainer.appendChild(cardDiv);
                });
            }
        }
    }

    fetchGameState();
    setInterval(fetchGameState, 3000);
});