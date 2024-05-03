 // Lego version 1.0.0
  import { h, Component } from './lego.min.js'
   
    import { render } from './lego.min.js';
   
    Component.prototype.render = function(state)
    {
      const childs = Array.from(this.childNodes);
      this.__originalChildren = childs.length && !this.__originalChildren?.length ? childs : this.__originalChildren;

       this.__state.slotId = `slot_${performance.now().toString().replace('.','')}_${Math.floor(Math.random() * 1000)}`;
   
      this.setState(state);
      if(!this.__isConnected) return
   
      const rendered = render([
        this.vdom({ state: this.__state }),
        this.vstyle({ state: this.__state }),
      ], this.document);
   
      const slot = this.document.querySelector(`#${this.__state.slotId}`);
      if (slot)
         for (const c of this.__originalChildren)
             slot.appendChild(c);
            
      return rendered;
    };

  
    const state =
    {
        page_id: null,
        page_id_en: null,
        remove: false,
        searchPagesEnabled: false,
        searchPagesEnglishEnabled: false,

        lang: {}
    };

    const methods =
    {
        changeField(e)
        {
            if (e.target.type == 'checkbox')
                this.render({ ...this.state, [ e.target.name ]: e.target.checked });
            else
                this.render({ ...this.state, [e.target.name]: e.target.value });
        },

        searchBtnClicked(e)
        {
            this.render({ ...this.state, searchPagesEnabled: !this.state.searchPagesEnabled });
        },

        searchBtn2Clicked(e)
        {
            this.render({ ...this.state, searchPagesEnglishEnabled: !this.state.searchPagesEnglishEnabled });
        },

        setPageId(id)
        {
            this.render({ ...this.state, page_id: Number(id) });
        },

        setPageIdEnglish(id)
        {
            this.render({ ...this.state, page_id_en: Number(id) });
        },

        submit(e)
        {
            e.preventDefault();

            if ((!this.state.page_id || !this.state.page_id_en) && !this.state.remove)
            {
                AbelMagazine.Alerts.push(AbelMagazine.Alerts.types.error, "Especifique uma página para os dois idiomas ou marque a opção de remover.");
                return;
            }

            const data =  { 'page_id': this.state.page_id, 'page_id_en': this.state.page_id_en, 'remove': this.state.remove };

            import(AbelMagazine.functionUrl("/admin/panel/pages"))
            .then(module => module.setEditorialTeamPageId(data))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .catch(AbelMagazine.Alerts.pushError(this.state.lang.forms.errorSettingSubmissionRulesPage));
        }
    };

    function setup()
    {
        this.state = { ...this.state, lang: JSON.parse(this.getAttribute('langJson')) };        
    }


  const __template = function({ state }) {
    return [  
    h("form", {"onsubmit": this.submit.bind(this)}, [
      h("ext-label", {"label": `${state.lang.forms.pageId} (pt-BR)`}, [
        h("input", {"type": `number`, "min": `1`, "step": `1`, "name": `page_id`, "value": state.page_id, "oninput": this.changeField.bind(this)}, ""),
        h("button", {"type": `button`, "class": `btn ml-2`, "onclick": this.searchBtnClicked.bind(this)}, `${state.lang.forms.search}`)
      ]),
      ((state.searchPagesEnabled) ? h("page-client-select", {"set_id_field_callback": this.setPageId.bind(this), "lang": state.lang}, "") : ''),
      h("ext-label", {"label": `${state.lang.forms.pageId} (en-US)`}, [
        h("input", {"type": `number`, "min": `1`, "step": `1`, "name": `page_id_en`, "value": state.page_id_en, "oninput": this.changeField.bind(this)}, ""),
        h("button", {"type": `button`, "class": `btn ml-2`, "onclick": this.searchBtn2Clicked.bind(this)}, `${state.lang.forms.search}`)
      ]),
      ((state.searchPagesEnglishEnabled) ? h("page-client-select", {"set_id_field_callback": this.setPageIdEnglish.bind(this), "lang": state.lang}, "") : ''),
      h("ext-label", {"label": `${state.lang.forms.remove}`, "reverse": `1`}, [
        h("input", {"type": `checkbox`, "name": `remove`, "value": `1`, "onchange": this.changeField.bind(this)}, "")
      ]),
      h("div", {"class": `text-center mt-4`}, [
        h("button", {"type": `submit`, "class": `btn`}, `${state.lang.forms.save}`)
      ])
    ])
  ]
  }

  const __style = function({ state }) {
    return h('style', {}, `
      
      
    `)
  }

  // -- Lego Core
  export default class Lego extends Component {
    init() {
      this.useShadowDOM = false
      if(typeof state === 'object') this.__state = Object.assign({}, state, this.__state)
      if(typeof methods === 'object') Object.keys(methods).forEach(methodName => this[methodName] = methods[methodName])
      if(typeof connected === 'function') this.connected = connected
      if(typeof setup === 'function') setup.bind(this)()
    }
    get vdom() { return __template }
    get vstyle() { return __style }
  }
  // -- End Lego Core

  
