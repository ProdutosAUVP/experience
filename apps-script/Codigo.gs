/** AUVP Experience — recebe os formulários de sugestão de destino.
 *
 *  Este arquivo é a cópia versionada do que está no projeto do Apps
 *  Script preso à planilha. O Google não lê daqui: ao mudar algo,
 *  copie para lá e **publique uma nova versão** (Implantar → Gerenciar
 *  implantações → ✏️ → Versão: Nova versão). Sem isso a URL do /exec
 *  continua servindo o código antigo — é o erro nº 1 daqui.
 *
 *  A URL do /exec está no `action` dos dois formulários, no index.html
 *  e no missao-china.html.
 */

/* Deixe vazio se este projeto nasceu de Extensões → Apps Script de
   dentro da planilha: aí `getActive()` acha a planilha sozinho.
   Se o projeto for avulso (criado em script.google.com), `getActive()`
   devolve null e nada é gravado — nesse caso ponha aqui o id da
   planilha, que é o trecho entre /d/ e /edit na URL dela. */
const PLANILHA_ID = '';

const ABA = 'Sugestões';
const COLUNAS = ['Data', 'Origem', 'Nome', 'E-mail', 'Telefone',
                 'Destinos marcados', 'Sugestão', 'Assunto'];

/** O envio dos formulários cai aqui. */
function doPost(e) {
  try {
    gravar((e && e.parameter) || {}, (e && e.parameters) || {});
    return json({ ok: true });
  } catch (erro) {
    // Aparece em "Execuções", no menu da esquerda do editor. É lá que se
    // olha quando o site diz que enviou e a planilha não tem a linha.
    console.error(erro);
    return json({ ok: false, erro: String(erro) });
  }
}

/** Abrir a URL do /exec no navegador cai aqui: é o teste de vida.
 *  Acrescentar ?teste=1 grava uma linha, provando a ponta a ponta. */
function doGet(e) {
  const p = (e && e.parameter) || {};

  if (p.teste === '1') {
    try {
      gravar({
        origem: 'teste-doGet',
        nome: 'Fulano de Teste',
        telefone: '(00) 00000-0000',
        sugestao: 'Linha de teste, pode apagar',
        email: 'teste@exemplo.com',
        assunto: 'Teste de ligação',
      }, {});
      return texto('OK — gravei uma linha de teste na aba "' + ABA + '".');
    } catch (erro) {
      return texto('FALHOU ao gravar: ' + erro);
    }
  }

  try {
    const f = folha();
    return texto([
      'No ar.',
      'Planilha: ' + f.getParent().getName(),
      'Aba: ' + f.getName(),
      'Linhas gravadas: ' + Math.max(0, f.getLastRow() - 1),
      '',
      'Para gravar uma linha de teste, acrescente ?teste=1 nesta URL.',
    ].join('\n'));
  } catch (erro) {
    return texto('No ar, mas NÃO consigo abrir a planilha: ' + erro +
      '\n\nSe diz que a planilha é nula, preencha PLANILHA_ID no topo do arquivo.');
  }
}

function gravar(p, ps) {
  if (p._isca) return;                 // armadilha de robô: descarta calado

  const trava = LockService.getScriptLock();
  trava.waitLock(30000);               // dois envios ao mesmo tempo não
  try {                                // podem disputar a mesma linha
    // A ordem tem de bater com COLUNAS, linha a linha.
    folha().appendRow([
      new Date(),
      p.origem || '',
      p.nome || '',
      p.email || '',
      p.telefone || '',
      (ps['destinos[]'] || []).join(', '),
      p.sugestao || '',
      p.assunto || '',
    ]);
  } finally {
    trava.releaseLock();
  }
}

function folha() {
  const planilha = PLANILHA_ID
    ? SpreadsheetApp.openById(PLANILHA_ID)
    : SpreadsheetApp.getActive();

  if (!planilha) {
    throw new Error('Planilha nula — o projeto não está preso a nenhuma. ' +
      'Preencha PLANILHA_ID no topo do arquivo.');
  }

  let f = planilha.getSheetByName(ABA);
  if (!f) {
    f = planilha.insertSheet(ABA);
    cabecalho(f);
    return f;
  }

  // Aba que já existe com o cabeçalho de uma versão anterior: reescreve.
  // Sem isso, campos novos entram sob rótulos velhos e a planilha mente.
  // Atenção: as linhas gravadas antes continuam na ordem antiga.
  const atual = f.getRange(1, 1, 1, COLUNAS.length).getValues()[0];
  if (atual.join('|') !== COLUNAS.join('|')) cabecalho(f);
  return f;
}

/** Rode este pelo editor (▶) para provar que a gravação funciona com as
 *  suas permissões, sem passar pela web. */
function cabecalho(f) {
  f.getRange(1, 1, 1, COLUNAS.length).setValues([COLUNAS]).setFontWeight('bold');
  f.setFrozenRows(1);
}

function testarGravacao() {
  gravar({
    origem: 'teste-editor',
    nome: 'Fulano de Teste',
    telefone: '(00) 00000-0000',
    sugestao: 'Linha de teste, pode apagar',
    email: 'teste@exemplo.com',
    assunto: 'Teste de ligação',
  }, {});
  console.log('Gravou. Confira a aba "' + ABA + '".');
}

const json = (d) => ContentService.createTextOutput(JSON.stringify(d))
  .setMimeType(ContentService.MimeType.JSON);
const texto = (s) => ContentService.createTextOutput(s)
  .setMimeType(ContentService.MimeType.TEXT);
