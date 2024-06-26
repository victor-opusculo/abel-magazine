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
        magazine_id: null,
        remove: false,

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

        submit(e)
        {
            e.preventDefault();

            if ((!this.state.magazine_id) && !this.state.remove)
            {
                AbelMagazine.Alerts.push(AbelMagazine.Alerts.types.error, "Especifique uma página para os dois idiomas ou marque a opção de remover.");
                return;
            }

            const data =  { 'magazine_id': this.state.magazine_id, 'remove': this.state.remove };

            import(AbelMagazine.functionUrl("/admin/panel/magazines"))
            .then(module => module.setDefaultMagazine(data))
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
      h("ext-label", {"label": `${state.lang.forms.magazineId}`}, [
        h("input", {"type": `number`, "min": `1`, "step": `1`, "name": `magazine_id`, "value": state.magazine_id, "oninput": this.changeField.bind(this)}, "")
      ]),
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

  
