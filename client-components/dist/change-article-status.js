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
        article_id: null,
        to_status: null,
        label: '',
        error: ''
    };

    const methods =
    {
        change()
        {
            import(AbelMagazine.functionUrl(`/admin/panel/articles/${this.state.article_id}`))
            .then(({ changeStatus }) => changeStatus({ articleId: this.state.article_id, toStatus: this.state.to_status }))
            .then(AbelMagazine.Alerts.pushFromJsonResult)
            .then(() => window.location.reload())
            .catch(AbelMagazine.Alerts.pushError(this.state.error));
        }
    };


  const __template = function({ state }) {
    return [  
    h("button", {"class": `btn mr-2`, "onclick": this.change.bind(this), "type": `button`}, `${state.label}`)
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

  
